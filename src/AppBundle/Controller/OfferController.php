<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Offer;
use AppBundle\Entity\Service;
use AppBundle\Entity\ServiceCategory;
use AppBundle\Entity\ServiceSnapshot;
use AppBundle\Form\CreateOfferForm;
use AppBundle\Form\DataTransformer\ItemToNameTransformer;
use AppBundle\Form\PickServiceType;
use AppBundle\Service\ItemSnapshotManager;
use AppBundle\Service\OfferManager;
use AppBundle\Service\ServiceSnapshotManager;
use AppBundle\Service\SnapshotManageruse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends Controller
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/offer/categories", name="offer_categories")
     */
    public function listCategoryAction(Request $request, ItemSnapshotManager $itemSnapshotManager, ServiceSnapshotManager $serviceSnapshotManager)
    {
        $categoryRepository = $this->em->getRepository(ServiceCategory::class);
        $data = array();

        $form = $this->createForm(PickServiceType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            /**
             * @var Service $service
             */
            $service = $data['service'];

            if ($service) {
                /**
                 * TODO: Snapshots must belong to a group of users through @owners attribute, many-to-many relationship. Required to lock "items to promised price"
                 */
                $serviceSnapshot = $serviceSnapshotManager->getCurrentSnapshot($service);
                if (!$serviceSnapshot) {
                    $serviceSnapshot = $serviceSnapshotManager->createSnapshot($service);
                    $this->em->persist($serviceSnapshot);
                }

                foreach ($service->getItems() as $costItem){
                    $itemSnapShot = $itemSnapshotManager->getCurrentSnapshot($costItem);
                    if (!$itemSnapShot or $itemSnapShot->getServiceSnapshot() != $serviceSnapshot) {
                        $itemSnapShot = $itemSnapshotManager->createSnapshot($costItem, $serviceSnapshot);
                        $this->em->persist($itemSnapShot);
                    }
                }


                $this->em->flush();
                $this->em->refresh($serviceSnapshot);

                return $this->redirectToRoute('offer_create', ['snapshotId' => $serviceSnapshot->getId()]);
            }

            $newForm = $this->createForm(PickServiceType::class, $data);

            if($request->isXmlHttpRequest()) {
                return $this->render(
                    'form_view.html.twig',
                    array(
                        'form' => $newForm->createView(),
                    )
                );
            }
        }

        return $this->render(
            'categories.html.twig',
            array(
                'form' => $form->createView(),

            )
        );
    }


    /**
     * @Route("/offer/service/snapshot/{snapshotId}/create", name="offer_create", requirements={"snapshotId"="\d+"})
     */
    public function offerCreateAction(Request $request, int $snapshotId, OfferManager $offerManager, ServiceSnapshotManager $serviceSnapshotManager)
    {
        $serviceRepository = $this->em->getRepository(Service::class);
        $serviceSnapshotRepository = $this->em->getRepository(ServiceSnapshot::class);

        /**
         * @var ServiceSnapshot $serviceSnapshot
         */
        $serviceSnapshot = $serviceSnapshotRepository->findOneById($snapshotId);

        if (!$serviceSnapshot) {
            return $this->createNotFoundException("Service not found.");
        }

        $offer = $offerManager->createOffer($serviceSnapshot);

        $singlePriceOfferItems = $offerManager->getSinglePriceOfferItems($offer);
        $rentableOfferItems = $offerManager->getRentableOfferItems($offer);

        $offerForm = $this->getOfferForm($rentableOfferItems);
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
            $this->addFlash(
                'success',
                'Order created successfully!'
            );

            return $this->redirectToRoute('offer_edit', ['offerId' => $offer->getId()]);
        }
        return $this->render('offer.html.twig', array(
            'form' => $offerForm->createView(),
            'offer' => $offer,
            'singlePriceOfferItems' => $singlePriceOfferItems,
            'rentableOfferItems' => $rentableOfferItems,
        ));
    }

    /**
     * @Route("/offer/{offerId}/edit", name="offer_edit", requirements={"offerId"="\d+"})
     */
    public function offerEditAction(Request $request, int $offerId, OfferManager $offerManager, ServiceSnapshotManager $serviceSnapshotManager)
    {
        $offerRepository = $this->em->getRepository(Offer::class);
        /**
         * @var Offer $offer
         */
        $offer = $offerRepository->findOneById($offerId);
        if (!$offer) {
            return $this->createNotFoundException("Offer not found.");
        }

        $singlePriceOfferItems = $offerManager->getSinglePriceOfferItems($offer);
        $rentableOfferItems = $offerManager->getRentableOfferItems($offer);
        $offerForm = $this->getOfferForm($rentableOfferItems);
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
            $this->addFlash(
                'success',
                'Order edited successfully!'
            );
        }
        return $this->render('offer.html.twig', array(
            'form' => $offerForm->createView(),
            'offer' => $offer,
            'singlePriceOfferItems' => $singlePriceOfferItems,
            'rentableOfferItems' => $rentableOfferItems,
        ));
    }


    private function getOfferForm($rentableItems)
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
                        'class' => 'time',
                        'offerItem' => $rentableItem->getId() ?? null,
                        'itemSnapshot' => $itemSnapshot->getId() ?? null,
                        'costItem' => $itemSnapshot->getCostItem()->getId() ?? null,
                    ],
                    'label' => false
                ]
            );
        }

        $form->add('save', SubmitType::class);
        return $form->getForm();
    }


}
