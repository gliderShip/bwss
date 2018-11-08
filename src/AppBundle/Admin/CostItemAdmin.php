<?php

namespace AppBundle\Admin;

use AppBundle\Model\Billable;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;

class CostItemAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('service')
            ->add('price')
            ->add('priceType',null, ['field_type' => ChoiceType::class, 'field_options' =>
                [
                    'choices' => Billable::BILLABLE_TYPES,
                ]
                    ]
            )
            ->add('currency')
            ->add('vat');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('name')
            ->add('service')
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
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('service')
            ->add('price', MoneyType::class,
                [
                    'help' => 'Price must include VAT',
                    "currency" => false,
                    "scale" => 2
                ]
            )
            ->add('priceType', ChoiceType::class,
                [
                    'choices' => Billable::BILLABLE_TYPES,
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
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('service')
            ->add('price', MoneyType::class)
            ->add('priceIncludesVat')
            ->add('netPrice', MoneyType::class)
            ->add('grossPrice', MoneyType::class)
            ->add('priceType')
            ->add('currency', CurrencyType::class)
            ->add('vat', PercentType::class);
    }
}
