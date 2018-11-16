<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;

class OfferAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('list', 'show'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
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
            ->add('serviceSnapshot')
            ->add('offerItems')
            ->add('extraSnapshots')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('subTotal')
            ->add('grandTotal')
            ->add('vatAmount')
            ->add('serviceSnapshot', null, array('route'=>array('name'=>'show')))
            ->add('offerItems', null, array('route'=>array('name'=>'show')))
            ->add('extraSnapshots', null, array('route'=>array('name'=>'show')))
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

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('subTotal')
            ->add('grandTotal')
            ->add('vatAmount')
            ->add('serviceSnapshot', null, array('route'=>array('name'=>'show')))
            ->add('offerItems', null, array('route'=>array('name'=>'show')))
            ->add('extraSnapshots', null, array('route'=>array('name'=>'show')))
            ->add('createdAt', 'datetime', array('format' => 'Y/m/d H:i:s'))
            ->add('updatedAt', 'datetime', array('format' => 'Y/m/d H:i:s'))
        ;
    }
}
