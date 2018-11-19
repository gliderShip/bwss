<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Discount;
use AppBundle\Entity\Extra;
use AppBundle\Entity\Offer;
use AppBundle\Entity\OfferItem;
use AppBundle\Entity\Service;
use AppBundle\Entity\ServiceCategory;
use AppBundle\Entity\ServiceSnapshot;
use AppBundle\Form\CreateOfferForm;
use AppBundle\Form\DataTransformer\ItemToNameTransformer;
use AppBundle\Form\PickServiceType;
use AppBundle\Service\CategorySnapshotManager;
use AppBundle\Service\DiscountManager;
use AppBundle\Service\ExtraManager;
use AppBundle\Service\ExtraSnapshotManager;
use AppBundle\Service\ItemSnapshotManager;
use AppBundle\Service\OfferManager;
use AppBundle\Service\ServiceSnapshotManager;
use AppBundle\Service\SnapshotManageruse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;

class OfferController extends Controller
{
    private $em;

    /** @var ItemSnapshotManager $ism */
    private $ism;

    /** @var ServiceSnapshotManager $ssm */
    private $ssm;

    /** @var ExtraSnapshotManager $esm */
    private $esm;

    /** @var CategorySnapshotManager $csm */
    private $csm;

    /** @var DiscountManager $dm */
    private $dm;

    public function __construct(
        EntityManagerInterface $em,
        ItemSnapshotManager $ism,
        ServiceSnapshotManager $ssm,
        ExtraSnapshotManager $esm,
        CategorySnapshotManager $csm,
        OfferManager $om,
        DiscountManager $dm
    )
    {
        $this->em = $em;
        $this->ism = $ism;
        $this->ssm = $ssm;
        $this->esm = $esm;
        $this->csm = $csm;
        $this->om = $om;
        $this->dm = $dm;
    }

    /**
     * @Route("/offer/categories", name="offer_categories")
     */
    public function listCategoryAction(Request $request)
    {
        $form = $this->createForm(PickServiceType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if (!$form->isValid()) {
                return $this->render('form_view.html.twig', array('form' => $form->createView(),));
            }

            $data = $form->getData();
            /** @var Service $service */
            $service = $data['service'];

            if ($service) {
                /** @var Category $category */
                $category = $data['category'];
                /** @var Extra[] $extras */
                $extras = $data['extras'];

                $serviceSnapshot = $this->ssm->getCurrentSnapshot($service);
                /** TODO: Snapshots must belong to a group of users through @owners attribute, many-to-many relationship. Required to lock "items to promised price" */
                if ($serviceSnapshot->getId() == null) {
                    $this->em->flush();
                }

                $categorySnapshot = $this->csm->getCurrentSnapshot($category);

                $this->ism->generateItemSnapshots($service, $serviceSnapshot);

                $sExtras = array();

                foreach ($extras as $extra) {
                    $sExtras[] = $this->esm->getCurrentSnapshot($extra, $categorySnapshot);
                }

                $this->em->flush();

                $sExtrasIds = array();
                foreach ($sExtras as $sExtra) {
                    $sExtrasIds[] = $sExtra->getId();
                }

                return $this->redirectToRoute('offer_create', ['snapshotId' => $serviceSnapshot->getId(), 'extras' => json_encode($sExtrasIds)]);
            }

            $newForm = $this->createForm(PickServiceType::class, $data);

            if ($request->isXmlHttpRequest()) {
                return $this->render('form_view.html.twig', ['form' => $newForm->createView()]);
            }
        }

        return $this->render('categories.html.twig', ['form' => $form->createView()]);
    }


    /**
     * @Route("/offer/service/snapshot/{snapshotId}/create", name="offer_create", requirements={"snapshotId"="\d+"})
     */
    public function offerCreateAction(Request $request, int $snapshotId)
    {
        $serviceRepository = $this->em->getRepository(Service::class);
        $serviceSnapshotRepository = $this->em->getRepository(ServiceSnapshot::class);

        /** @var ServiceSnapshot $serviceSnapshot */
        $serviceSnapshot = $serviceSnapshotRepository->findOneById($snapshotId);

        if (!$serviceSnapshot) {
            return $this->createNotFoundException("Service not found.");
        }

        $jsonSelectedSextras = $request->query->get('extras');
        $selectedSextras = $this->esm->getRequestSextras($jsonSelectedSextras, $serviceSnapshot->getCategorySnapshot());

        $offer = $this->om->createOffer($serviceSnapshot, $selectedSextras);

        $offerItems = $offer->getOfferItems();
        $singlePriceOfferItems = $this->om->getSinglePriceOfferItems($offer);
        $rentableOfferItems = $this->om->getRentableOfferItems($offer);

        $offerForm = $this->getOfferForm($offerItems, $rentableOfferItems);
        $offerForm->handleRequest($request);
        if ($offerForm->isSubmitted() && $offerForm->isValid()) {
            $formData = $offerForm->getData();
            foreach ($rentableOfferItems as $rentableItem) {
                $costItemSnapshot = $rentableItem->getItemSnapshot();
                // TODO: Validate Form. Contains all rentable items names and hours etc.
                $rentableItem->setHours($formData[$costItemSnapshot->getName()]);
            }
            $this->em->persist($offer);
            $this->em->flush();
            $this->addFlash('success', 'Order created successfully!');

            return $this->redirectToRoute('offer_edit', ['offerId' => $offer->getId()]);
        }

        return $this->render('offer.html.twig', array(
            'form' => $offerForm->createView(),
            'offer' => $offer,
            'singlePriceOfferItems' => $singlePriceOfferItems,
            'rentableOfferItems' => $rentableOfferItems,
            'selectedSextras' => $selectedSextras,

        ));
    }

    /**
     * @Route("/offer/{offerId}/edit", name="offer_edit", requirements={"offerId"="\d+"})
     */
    public function offerEditAction(Request $request, int $offerId)
    {
        $offerRepository = $this->em->getRepository(Offer::class);

        /**
         * @var Offer $offer
         */
        $offer = $offerRepository->findOneById($offerId);
        if (!$offer) {
            return $this->createNotFoundException("Offer not found.");
        }

        $selectedSextras = $offer->getExtraSnapshots();
        $offerItems = $offer->getOfferItems();
        $singlePriceOfferItems = $this->om->getSinglePriceOfferItems($offer);
        $rentableOfferItems = $this->om->getRentableOfferItems($offer);
        $offerForm = $this->getOfferForm($offerItems, $rentableOfferItems, 'Update');
        $offerForm->handleRequest($request);
        if ($offerForm->isSubmitted() && $offerForm->isValid()) {
            $formData = $offerForm->getData();

            foreach ($rentableOfferItems as $rentableItem) {
                $costItemSnapshot = $rentableItem->getItemSnapshot();
                // TODO: Validate Form. Contains all rentable items names and hours etc.
                $rentableItem->setHours($formData[$costItemSnapshot->getName()]);
            }

            foreach ($offerItems as $offerItem) {
                $ci = $offerItem->getItemSnapshot()->getCostItem();
                $formKey = 'item-'.$ci->getId().'-discount';
                if(!empty($formData[$formKey])){
                    $offerItem->setDiscount($formData[$formKey]);
                }
            }


            $this->em->persist($offer);
            $this->em->flush();
            $this->addFlash('success', 'Order edited successfully!');
        }

        return $this->render('offer.html.twig', array(
            'form' => $offerForm->createView(),
            'offer' => $offer,
            'singlePriceOfferItems' => $singlePriceOfferItems,
            'rentableOfferItems' => $rentableOfferItems,
            'selectedSextras' => $selectedSextras,
        ));
    }


    private function getOfferForm($offerItems, $rentableItems, $actionLabel = 'Create')
    {
        $formData = array();
        foreach ($rentableItems as $rentableItem) {
            $itemSnapshot = $rentableItem->getItemSnapshot();
            $formData[$itemSnapshot->getName()] = $rentableItem->getHours();
        }

        $form = $this->createFormBuilder($formData);

        foreach ($rentableItems as $rentableItem) {
            $itemSnapshot = $rentableItem->getItemSnapshot();
            $form->add($itemSnapshot->getName(), IntegerType::class, [
                    'attr' => [
                        'class' => 'time customizable',
                        'offerItem' => $rentableItem->getId() ?? null,
                        'itemSnapshot' => $itemSnapshot->getId() ?? null,
                        'costItem' => $itemSnapshot->getCostItem()->getId() ?? null,
                    ],
                    'constraints' => [
                        new Assert\GreaterThanOrEqual(['value' => 1]),
                    ],
                    'label' => false
                ]

            );
        }

        /** @var OfferItem $offerItem */
        foreach ($offerItems as $offerItem) {
            $ci = $offerItem->getItemSnapshot()->getCostItem();
            $discounts = $this->dm->getValidDisounts($ci);
            if ($ci->isDiscountable() and !empty($discounts)) {
                $form->add(
                    'item-' . $ci->getId() . '-discount',
                    EntityType::class,
                    [
                        'class' => Discount::class,
                        'label' => 'Pick a discount',
                        'placeholder' => '',
                        'choices' => $discounts,
                        'required' => false,
                        'choice_attr' => function($choiceValue, $key, $value) {
                            return ['discount' => $choiceValue->getPrice()];
                        },
                        'attr' => [
                            'class' => 'discount',
                            ]
                    ]
                );
            }
        }

        $form->add('save', SubmitType::class, ['label' => $actionLabel]);
        return $form->getForm();
    }


}
