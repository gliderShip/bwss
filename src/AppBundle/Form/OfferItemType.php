<?php

namespace AppBundle\Form;

use AppBundle\Entity\OfferItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('hours', NumberType::class);
//        $builder->add('itemSnapshot', null, ['by_reference'=>false]);
//        $builder->add('itemSnapshot', HiddenType::class,
//            [
//                'data' => '1234',
//                'mapped' => false,
//                ]
//        );
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => OfferItem::class,
        ));

//        $resolver->setRequired('itemSnapshot');
    }
}
