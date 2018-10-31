<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 10/31/2018
 * Time: 5:34 PM
 */

namespace AppBundle\Form;

use AppBundle\Entity\Offer;
use AppBundle\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateOfferForm extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        switch ($options['flow_step']) {
            case 1:
                $builder->add('service');
                break;
            case 2:
                break;
        }
    }

    public function getBlockPrefix() {
        return 'createOffer';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Offer::class,
        ));
    }

}