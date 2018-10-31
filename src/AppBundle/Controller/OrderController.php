<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Offer;
use AppBundle\Form\CreateOfferForm;
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
    public function indexAction(Request $request)
    {

        return $this->render('order.html.twig', array(
            'form' => $form->createView(),
            'flow' => $flow,
        ));
    }
}
