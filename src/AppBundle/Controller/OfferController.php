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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("/offer/categories", name="offer_categories")
     */
    public function listCategoryAction(Request $request, ItemSnapshotManager $itemSnapshotManager)
    {
        $categoryRepository = $this->em->getRepository(ServiceCategory::class);

        $category = $categoryRepository->getDefaultCategory();
        dump($category);

        $form = $this->getServiceForm($category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $category = $data['category'];
            dump($category);
            if(!$category){
                throw new Exception('Category');
            }
            $form = $this->getServiceForm($category);

            return $this->render('form_view.html.twig', array(
                'form' => $form->createView(),
            ));
        }

        return $this->render('categories.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/offer/eeee/{categoryId}/services", name="offer_services", requirements={"categoryId"="\d+"})
     */
    public function listCategoryServicesAction(int $categoryId)
    {

        $categoryRepository = $this->em->getRepository(ServiceCategory::class);

        $category = $categoryRepository->findOneById($categoryId);

        if (!$category) {
            return $this->createNotFoundException("Service category not found.");
        }

        $form = $this->getServiceForm($category);


        return $this->render('form_view.html.twig', array(
            'form' => $form->createView(),
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

        $offerForm = $this->getForm($rentableOfferItems, $this->generateUrl('offer_create', ['serviceId' => $serviceId]));
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

        $offerForm = $this->getForm($rentableOfferItems, $this->generateUrl('offer_edit', ['offerId' => $offerId]));
        $offerForm->handleRequest($request);

        if ($offerForm->isSubmitted() && $offerForm->isValid()) {

            $formData = $offerForm->getData();

            dump($formData);
            die;

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


//    private function getForm($rentableItems, $postUrl)
//    {
//        $formData = array();
//
//        foreach ($rentableItems as $rentableItem) {
//            $itemSnapshot = $rentableItem->getItemSnapshot();
//            $formData[$itemSnapshot->getName()] = $rentableItem->getHours();
//        }
//
//        $form = $this->createFormBuilder($formData,
//            [
//                'attr' => [
//                    'ic-post-to' => $postUrl,
//                    'ic-target' => '#replace'
//                ],
//            ]
//        );
//        foreach ($rentableItems as $rentableItem) {
//
//            $itemSnapshot = $rentableItem->getItemSnapshot();
//            $form->add($itemSnapshot->getName(), IntegerType::class, [
//                    'attr' => [
//                        'class' => 'time',
//                        'offerItem' => $rentableItem->getId() ?? null,
//                        'itemSnapshot' => $itemSnapshot->getId() ?? null,
//                        'costItem' => $itemSnapshot->getCostItem()->getId() ?? null,
//                    ],
//                    'label' => false
//                ]
//            );
//        }
//
//        $form->add('save', SubmitType::class,
//            [
//                'attr' => ['id' => ''],
//            ]
//        );
//
//        return $form->getForm();
//    }

    private function getServiceForm(ServiceCategory $category)
    {
        $services = $category->getServices();
        $serviceChoices = array();
        foreach ($services as $service){
            $serviceChoices[$service->getName()] = $service->getId();
        }

        $serviceChoices['fantom'] = 33;

        $form = $this->createFormBuilder(null, ['validation_groups' => false, 'csrf_protection' => false])
            ->add('category', EntityType::class, [
                'class' => ServiceCategory::class,
//                'label' => false,
                'label' => 'Service Category',
                'data' => $category,
            ])
            ->add('services', ChoiceType::class, [
                'label' => 'SubService',
                'choices' => $serviceChoices,
                'empty_data' => '27',
//                'data' => $category->getServices()->first(),
            ])
            ->add('next', SubmitType::class, [
                'label' => 'Next',
                'attr' => ['class' => 'btn btn-success']
            ])
            ->getForm();

        return $form;
    }

}
