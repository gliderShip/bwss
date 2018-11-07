<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CostItem;
use AppBundle\Entity\ItemSnapshot;
use AppBundle\Entity\Offer;
use AppBundle\Entity\OfferItem;
use AppBundle\Entity\Service;
use AppBundle\Entity\ServiceCategory;
use AppBundle\Form\CreateOfferForm;
use AppBundle\Form\DataTransformer\ItemToNameTransformer;
use AppBundle\Form\OfferType;
use AppBundle\Model\Billable;
use AppBundle\Repository\ServiceCategoryRepository;
use AppBundle\Service\CategorySnapshotManager;
use AppBundle\Service\ItemSnapshotManager;
use AppBundle\Service\OfferManager;
use AppBundle\Service\ServiceSnapshotManager;
use AppBundle\Service\SnapshotManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends Controller
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/offer/service-categories", name="offer_categories")
     */
    public function listCategoryAction(Request $request, ItemSnapshotManager $itemSnapshotManager)
    {
        $this->em = $this->get('doctrine.orm.entity_manager');
        $categoryRepository = $this->em->getRepository(ServiceCategory::class);
        $serviceCategories = $categoryRepository->findAll();

        return $this->render('categories.html.twig', array(
            'categories' => $serviceCategories
        ));


        $costItemRepository = $this->em->getRepository(CostItem::class);
        $costItem = $costItemRepository->findAll()[0];
        $itemSnapshot = $itemSnapshotManager->getCurrentSnapshot($costItem);
        $this->em->persist($itemSnapshot);
        $this->em->flush();

        return $this->render('offer.html.twig', array(
            'form' => $form->createView(),
            'flow' => $flow,
        ));
    }

    /**
     * @Route("/offer/service-categories/{categoryId}/services", name="category_services", requirements={"categoryId"="\d+"})
     */
    public function listCategoryServicesAction(int $categoryId)
    {

        $categoryRepository = $this->em->getRepository(ServiceCategory::class);

        $category = $categoryRepository->findOneById($categoryId);

        if (!$category) {
            return $this->createNotFoundException("Service category not found.");
        }

        $services = $category->getServices();

        return $this->render('_services.html.twig', array(
            'services' => $services
        ));
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

//        $uri = $request->getUri();

        $offerForm = $this->getForm( $rentableOfferItems);
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
            return $this->createNotFoundException("Service not found.");
        }

        $singlePriceOfferItems = $offerManager->getSinglePriceOfferItems($offer);
        $rentableOfferItems = $offerManager->getRentableOfferItems($offer);

        $offerForm = $this->getForm( $rentableOfferItems);
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
        }

        return $this->render('offer.html.twig', array(
            'form' => $offerForm->createView(),
            'offer' => $offer,
            'singlePriceOfferItems' => $singlePriceOfferItems,
            'rentableOfferItems' => $rentableOfferItems,
        ));

    }




    private function getForm($rentableItems)
    {
        $formData = array();

        foreach ($rentableItems as $rentableItem){
            $itemSnapshot = $rentableItem->getItemSnapshot();
            $formData[$itemSnapshot->getName()] = $rentableItem->getHours();
        }

        $form = $this->createFormBuilder($formData);
        foreach ($rentableItems as $rentableItem) {

            $itemSnapshot = $rentableItem->getItemSnapshot();
            $form->add($itemSnapshot->getName(), IntegerType::class, [
                    'attr' => [
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
        $form->add('save', SubmitType::class);

        return $form->getForm();
    }


//    private function getForm(Offer $offer)
//    {
//
//        $offerId = $offer->getId();
//        $serviceSnapshotId = $offer->getServiceSnapshot()->getId();
//        $serviceId = $offer->getServiceSnapshot()->getService()->getId();
//        $rentableItems = array();
//
//        foreach ($offer->getItems() as $offerItem) {
//            $costItemSnapshot = $offerItem->getItemSnapshot();
//            if ($costItemSnapshot->isRentable()) {
//                $rentableItems[$costItemSnapshot->getName()] = $offerItem->getHours();
//            }
//        }
//
//        $form = $this->createFormBuilder($rentableItems);
//
//        foreach ($offer->getItems() as $offerItem) {
//
//            if ($offerItem->getItemSnapshot()->getPriceType() == Billable::BILLABLE_TYPES['HOURLY AMOUNT']) {
//
//                $itemSnapshot = $offerItem->getItemSnapshot();
//
//                $form->add($itemSnapshot->getName(), IntegerType::class, [
//                        'attr' => [
//                            'offerItem' => $offerItem->getId() ?? null,
//                            'itemSnapshot' => $itemSnapshot->getId() ?? null,
//                            'costItem' => $itemSnapshot->getCostItem()->getId() ?? null,
//                        ],
//                        'label' => false
//                    ]
//                );
//            }
//
//        }
//
//        $form->setAction($this->generateUrl('offer_create', ['serviceId' => $serviceId]));
//        $form->add('save', SubmitType::class);
//
//        return $form->getForm();
//    }


}
