<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CostItem;
use AppBundle\Entity\ItemSnapshot;
use AppBundle\Entity\Offer;
use AppBundle\Entity\OfferItem;
use AppBundle\Form\CreateOfferForm;
use AppBundle\Service\ItemSnapshotManager;
use AppBundle\Service\SnapshotManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order", name="order_")
 */
class OrderController extends Controller
{
    /**
     * @Route("/new", name="create")
     */
    public function indexAction(Request $request, ItemSnapshotManager $itemSnapshotManager)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $costItemRepository = $em->getRepository(CostItem::class);
        $costItem = $costItemRepository->findAll()[0];
        dump($costItem);
        $itemSnapshot = $itemSnapshotManager->getCurrentSnapshot($costItem);
        dump($itemSnapshot);
        $em->persist($itemSnapshot);
        $em->flush();
        dump($itemSnapshot);

        die;
        return $this->render('order.html.twig', array(
            'form' => $form->createView(),
            'flow' => $flow,
        ));
    }
}
