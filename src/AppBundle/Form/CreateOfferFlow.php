<?php

namespace AppBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

class CreateOfferFlow extends FormFlow
{

    protected function loadStepsConfig()
    {
        return array(
            array(
                'label' => 'ServiceCategory',
                'form_type' => 'AppBundle\Form\CreateOfferForm',
            ),
            array(
                'label' => 'Service',
                'form_type' => 'AppBundle\Form\CreateOfferForm',
            ),
            array(
                'label' => 'Hours',
                'form_type' => 'AppBundle\Form\CreateOfferForm',
            ),
            array(
                'label' => 'confirmation',
            ),
        );
    }

}