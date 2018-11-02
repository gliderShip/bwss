<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class ItemSnapshotAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
//        $collection->remove('edit');
        $collection->clearExcept(array('list', 'show', 'delete'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('version')
            ->add('name')
            ->add('costItem')
            ->add('serviceSnapshot')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('priceIncludesVat')
            ->add('price')
            ->add('priceType')
            ->add('currency')
            ->add('vat')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('version')
            ->add('name')
            ->add('costItem', null, array('route'=>array('name'=>'show')))
            ->add('serviceSnapshot', null, array('route'=>array('name'=>'show')))
            ->add('priceIncludesVat')
            ->add('price')
            ->add('priceType')
            ->add('currency')
            ->add('vat')
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
//            ->add('id')
//            ->add('version')
//            ->add('name')
//            ->add('createdAt')
//            ->add('updatedAt')
//            ->add('priceIncludesVat')
//            ->add('price')
//            ->add('priceType')
//            ->add('currency')
//            ->add('vat')
//        ;
//    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('version')
            ->add('name')
            ->add('costItem', null, array('route'=>array('name'=>'show')))
            ->add('serviceSnapshot', null, array('route'=>array('name'=>'show')))
            ->add('priceIncludesVat')
            ->add('price')
            ->add('priceType')
            ->add('currency')
            ->add('vat')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }
}
