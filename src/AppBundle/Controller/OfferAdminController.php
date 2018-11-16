<?php

namespace AppBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;

class OfferAdminController extends CRUDController
{
    public function editAction($id = null)
    {
        return $this->redirectToRoute('offer_edit', ['offerId' => $id]);
    }
}
