<?php

namespace AppBundle\Form;

use AppBundle\Entity\CostItem;
use AppBundle\Entity\ItemSnapshot;
use AppBundle\Entity\OfferItem;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $builder->add('serviceSnapshot');
            $builder->add('items', CollectionType::class,
                [
                    'entry_type' => OfferItemType::class,
                    'entry_options' => array('label' => false),
                ]


            );

        $builder->add('save', SubmitType::class);
    }

//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setRequired('items');
//
//    }

}
