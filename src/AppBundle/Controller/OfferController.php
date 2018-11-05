<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CostItem;
use AppBundle\Entity\ItemSnapshot;
use AppBundle\Entity\Offer;
use AppBundle\Entity\OfferItem;
use AppBundle\Entity\Service;
use AppBundle\Entity\ServiceCategory;
use AppBundle\Form\CreateOfferForm;
use AppBundle\Form\OfferType;
use AppBundle\Repository\ServiceCategoryRepository;
use AppBundle\Service\ItemSnapshotManager;
use AppBundle\Service\OfferManager;
use AppBundle\Service\ServiceSnapshotManager;
use AppBundle\Service\SnapshotManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

        $offerItems = $offerManager->getOfferItems($service);

        $offer = new Offer($serviceSnapshot,$offerItems);

        $offerForm = $this->createForm(OfferType::class, $offer);

        $offerForm->handleRequest($request);

        if ($offerForm->isSubmitted() && $offerForm->isValid()) {

            $data = $offerForm->getData();
            dump($offerForm->getViewData());

            dump($data);
            dump($offerForm);
            die;
        }

        return $this->render('offer.html.twig', array(
            'form' => $offerForm->createView(),
        ));

    }


    /**
     * @Route("/offer/services/{serviceId}", name="offer_service", requirements={"serviceId"="\d+"})
     */
//    public function getServiceAction(int $serviceId){
//
//        $serviceRepository =  $this->em->getRepository(Service::class);
//
//        $service = $serviceRepository->findOneById($serviceId);
//
//        if(!$service){
//            return $this->createNotFoundException("Service category not found.");
//        }
//
////        $services = $category->getServices();
//
//        return $this->render('_services.html.twig', array(
//            'services' => $services
//        ));
//    }


}
