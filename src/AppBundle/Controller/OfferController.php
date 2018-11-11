<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Service;
use AppBundle\Entity\ServiceCategory;
use AppBundle\Form\CreateOfferForm;
use AppBundle\Form\DataTransformer\ItemToNameTransformer;
use AppBundle\Form\OfferType;
use AppBundle\Service\ItemSnapshotManager;
use AppBundle\Service\OfferManager;
use AppBundle\Service\ServiceSnapshotManager;
use AppBundle\Service\SnapshotManageruse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
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
    public function listCategoryAction(Request $request, ItemSnapshotManager $itemSnapshotManager)
    {
        $categoryRepository = $this->em->getRepository(ServiceCategory::class);
        $category =  $categoryRepository->getDefaultCategory();
        $data = array();

        $form = $this->createForm(OfferType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $category = $data['category'];
            $service = $data['service'];

            if($service){
                return $this->redirectToRoute('offer_create', ['serviceId'=>$service->getId()]);
            }


            $newForm = $this->createForm(OfferType::class, $data);

            return $this->render(
                'form_view.html.twig',
                array(
                    'form' => $newForm->createView(),
                )
            );
        }

        return $this->render(
            'categories.html.twig',
            array(
                'form' => $form->createView(),

            )
        );


    }


    /**
     * @Route("/offer/service/{serviceId}/create", name="offer_create", requirements={"serviceId"="\d+"})
     */
    public function offerCreateAction(Request $request, int $serviceId, OfferManager $offerManager, ServiceSnapshotManager $serviceSnapshotManager)
    {
        $serviceRepository = $this->em->getRepository(Service::class);

        /**
         * @var Service $service
         */
        $service = $serviceRepository->findOneById($serviceId);

        if (!$service) {
            return $this->createNotFoundException("Service not found.");
        }

        $serviceSnapshot = $serviceSnapshotManager->getCurrentSnapshot($service);
        $offer = $offerManager->createOffer($serviceSnapshot);

        $singlePriceOfferItems = $offerManager->getSinglePriceOfferItems($offer);
        $rentableOfferItems = $offerManager->getRentableOfferItems($offer);

        $offerForm = $this->getForm($rentableOfferItems, $this->generateUrl('offer_create', ['serviceId'=>$serviceId]));
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
            return $this->forward('AppBundle:Offer:offerEdit', ['offerId' => $offer->getId()]);
//            return $this->redirectToRoute('offer_edit', ['offerId' => $offer->getId()]);
        }
        return $this->render('offer.html.twig', array(
            'form' => $offerForm->createView(),
            'offer' => $offer,
            'singlePriceOfferItems' => $singlePriceOfferItems,
            'rentableOfferItems' => $rentableOfferItems,
        ));
    }


    private function getForm($rentableItems, $postUrl)
    {
        $formData = array();
        foreach ($rentableItems as $rentableItem) {
            $itemSnapshot = $rentableItem->getItemSnapshot();
            $formData[$itemSnapshot->getName()] = $rentableItem->getHours();
        }
        $form = $this->createFormBuilder($formData,
            [
                'attr' => [
                    'ic-post-to' => $postUrl,
                    'ic-target' => '#replace'
                ],
            ]
        );
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
//        $form->setAction($uri);
//        $form->setMethod('POST');
        $form->add('save', SubmitType::class,
            [
                'attr' => ['id' => ''],
            ]
        );
        return $form->getForm();
    }



}
