<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Sonata\AdminBundle\Route\RouteCollection;

class OfferItemAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('list', 'show'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('hours')
            ->add('offer')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('hours')
            ->add('offer', null, array('route'=>array('name'=>'show')))
            ->add('itemSnapshot', null, array('route'=>array('name'=>'show')))
//            ->add('price', MoneyType::class)
            ->add('netPrice', MoneyType::class)
            ->add('grossPrice', MoneyType::class)
            ->add('priceType')
            ->add('currency', CurrencyType::class)
            ->add('vat', PercentType::class)
            ->add('createdAt')
            ->add('updatedAt')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

//    protected function configureFormFields(FormMapper $formMapper)
//    {
//        $formMapper
//            ->add('itemSnapshot')
//            ->add('hours')
//        ;
//    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('hours')
            ->add('offer', null, array('route'=>array('name'=>'show')))
            ->add('itemSnapshot', null, array('route'=>array('name'=>'show')))
//            ->add('price', MoneyType::class)
            ->add('netPrice', MoneyType::class)
            ->add('grossPrice', MoneyType::class)
            ->add('priceType')
            ->add('currency', CurrencyType::class)
            ->add('vat', PercentType::class)
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }
}
