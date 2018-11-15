<?php

namespace AppBundle\Admin;

use AppBundle\Model\Billable;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;

class ExtraAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('serviceCategory')
            ->add('price')
            ->add('priceType',null, ['field_type' => ChoiceType::class, 'field_options' =>
                    [
                        'choices' => Billable::BILLABLE_TYPES,
                    ]
                ]
            )
            ->add('currency')
            ->add('vat')
            ->add('createdAt',
                'doctrine_orm_datetime',
                array(
                    'field_type' => DateTimePickerType::class,
                    'field_options' => array('format' => 'yyyy/MM/dd H:mm:ss'),
                )
            )
            ->add('updatedAt',
                'doctrine_orm_datetime',
                array(
                    'field_type' => DateTimePickerType::class,
                    'field_options' => array('format' => 'yyyy/MM/dd H:mm:ss'),
                )
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('name')
            ->add('serviceCategory')
            ->add('price')
            ->add('priceIncludesVat')
            ->add('netPrice', MoneyType::class)
            ->add('grossPrice', MoneyType::class)
            ->add('priceType', ChoiceType::class,
                [
                    'choices' => Billable::BILLABLE_TYPES,
                ]
            )
            ->add('currency')
            ->add('vat')
            ->add('createdAt', 'datetime', array('format' => 'Y/m/d H:i:s'))
            ->add('updatedAt', 'datetime', array('format' => 'Y/m/d H:i:s'))
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('serviceCategory')
            ->add('price', MoneyType::class,
                [
                    'required' => true,
                    'help' => 'Price must include VAT',
                    "currency" => false,
                    "scale" => 2
                ]
            )
            ->add('priceType', ChoiceType::class,
                [
                    'choices' => Billable::BILLABLE_TYPES,
                    'attr' => array('readonly' => true), // Frontend
                    'disabled' => true,                     // Backend
                    'required' => false,
                ]
            )
            ->add('priceIncludesVat', null,
                [
                    'attr' => array('readonly' => true), // Frontend
                    'disabled' => true,                     // Backend
                    'required' => false,
                ]
            )
            ->add('currency', CurrencyType::class,
                [
                    'attr' => array('readonly' => true), // Frontend
                    'disabled' => true,                     // Backend
                    'required' => false,
                ]
            )
            ->add('vat', PercentType::class,
                [
                    'attr' => array('readonly' => true), // Frontend
                    'disabled' => true,                     // Backend
                    'required' => false,
                ]
            );
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('serviceCategory')
            ->add('price', MoneyType::class)
            ->add('priceIncludesVat')
            ->add('netPrice', MoneyType::class)
            ->add('grossPrice', MoneyType::class)
            ->add('priceType')
            ->add('currency', CurrencyType::class)
            ->add('vat', PercentType::class)
            ->add('createdAt', 'datetime', array('format' => 'Y/m/d H:i:s'))
            ->add('updatedAt', 'datetime', array('format' => 'Y/m/d H:i:s'))
        ;
    }
}
