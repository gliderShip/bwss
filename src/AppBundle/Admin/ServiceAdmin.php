<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Service;
use AppBundle\Repository\ServiceRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ServiceAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('serviceCategory')
            ->add('costItems')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('name')
            ->add('serviceCategory')
            ->add('costItems')
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
//        $query = $this->modelManager->getEntityManager($entity)->createQuery('SELECT s FROM MyCompany\MyProjectBundle\Entity\Seria s ORDER BY s.nameASC');

        $formMapper
            ->add('name')
            ->add('serviceCategory')
//            ->add('children')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('serviceCategory')
            ->add('costItems')
//            ->add('children')
        ;
    }



}
