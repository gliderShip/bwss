<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class OfferAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('serviceSnapshot')
            ->add('offerItems')
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
            ->add('offerItems')
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

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('serviceSnapshot')
            ->add('offerItems')
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
            ->add('offerItems')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }
}
