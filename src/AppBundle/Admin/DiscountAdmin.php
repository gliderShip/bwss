<?php

namespace AppBundle\Admin;

use AppBundle\Model\Billable;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class DiscountAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('code')
            ->add('price')
            ->add('costItems')
            ->add('startDate',
                'doctrine_orm_datetime',
                array(
                    'field_type' => DateTimePickerType::class,
                    'field_options' => array('format' => 'yyyy/MM/dd H:mm:ss'),
                )
            )
            ->add('endDate',
                'doctrine_orm_datetime',
                array(
                    'field_type' => DateTimePickerType::class,
                    'field_options' => array('format' => 'yyyy/MM/dd H:mm:ss'),
                )
            );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('name')
            ->add('code')
            ->add('price')
            ->add('costItems', null, array('route' => array('name' => 'show')))
            ->add('startDate', 'datetime', array('format' => 'Y/m/d H:i:s'))
            ->add('endDate', 'datetime', array('format' => 'Y/m/d H:i:s'))
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
            ->add('code')
            ->add('price', MoneyType::class,
                [
                    'required' => true,
                    "currency" => Billable::DEFAULT_CURRENCY,
                    "scale" => Billable::PRICE_SCALE,
                ]
            );
        if ($this->isCurrentRoute('create')) {
            // The thumbnail field will only be added when the edited item is created
            $formMapper->add('startDate', DateTimePickerType::class, array('format' => 'yyyy/MM/dd H:mm:ss', 'required' => true, 'dp_min_date' => new \DateTime('now')));
        } else {
            $formMapper->add('startDate', DateTimePickerType::class, array('format' => 'yyyy/MM/dd H:mm:ss', 'required' => true));
        };
        $formMapper->add('endDate', DateTimePickerType::class, array('format' => 'yyyy/MM/dd H:mm:ss', 'required' => true, 'dp_min_date' => new \DateTime('now')))
            ->add('costItems');
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('code')
            ->add('price')
            ->add('costItems', null, array('route' => array('name' => 'show')))
            ->add('startDate', 'datetime', array('format' => 'Y/m/d H:i:s'))
            ->add('endDate', 'datetime', array('format' => 'Y/m/d H:i:s'))
            ->add('currency');
    }
}
