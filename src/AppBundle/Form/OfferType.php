<?php

namespace AppBundle\Form;

use AppBundle\Entity\Service;
use AppBundle\Entity\ServiceCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'category',
            EntityType::class,
            [
                'class' => ServiceCategory::class,
                'label' => 'Service Category',
                'choice_label' => 'name',
//                'placeholder' => '',
            ]
        );


        $formModifier = function (FormInterface $form, ServiceCategory $category = null) {

            $services = $category ? $category->getServices() : array();

            $form->add(
                'service',
                EntityType::class,
                [
                    'class' => Service::class,
                    'label' => 'SubService',
                    'placeholder' => '',
                    'choice_label' => 'name',
                    'choices' => $services,
                ]
            );
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {

                $data = $event->getData();
                $form = $event->getForm();

//                dump($data);
//                die;

                $category = $data['category'] ?? null;
                $services = $category ? $category->getServices() : array();


                $formModifier($form, $category);
            }
        );

        
        $builder->get('category')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $category = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $category);
            }
        );

        $builder->add(
            'next',
            SubmitType::class,
            [
                'label' => 'Next',
                'attr' => ['class' => 'btn btn-success'],
            ]
        );

    }

//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setDefaults(
//            array(
//                'data_class' => null,
//            )
//        );
//
////        $resolver->setRequired('items');
//
//    }

}
