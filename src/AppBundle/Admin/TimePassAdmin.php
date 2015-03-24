<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class TimePassAdmin extends Admin
{
    protected $baseRouteName = 'timepass';
    protected $baseRoutePattern = 'timepass';

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('time_valid')
            ->add('voucher_code')
            ->add('revenue_model')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('category', 'entity', array('class' => 'AppBundle\Entity\Category'))
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('time_valid')
            ->add('voucher_code')
            ->add('revenue_model')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('category', 'entity', array('class' => 'AppBundle\Entity\Category'))
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('time_valid')
            ->add('voucher_code')
            ->add('revenue_model')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('category', 'entity', array('class' => 'AppBundle\Entity\Category'))
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('time_valid')
            ->add('voucher_code')
            ->add('revenue_model')
        ;
    }
}
