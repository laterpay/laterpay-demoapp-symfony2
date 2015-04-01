<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use AppBundle\Entity\Post;

class PostAdmin extends Admin
{
    protected $baseRouteName = 'post';
    protected $baseRoutePattern = 'post';

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('user.username')
            ->add('category.name')
            ->add('title')
            ->add('status')
            ->add('price')
            ->add('published_at', 'doctrine_orm_date')
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
            ->add('user.username')
            ->add('category.name')
            ->add('title')
            ->add('status')
            ->add('price')
            ->add('published_at')
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
            ->add('user', 'entity', array('class' => 'AppBundle\Entity\User'))
            ->add('category', 'entity', array('class' => 'AppBundle\Entity\Category'))
            ->add('title')
            ->add('content')
            ->add('teaser_content')
            ->add('price')
            ->add('published', 'checkbox', array('required' => false))
            ->add('revenue_model', 'choice', array(
                'required' => false,
                'choices' => array(
                    Post::RM_PPU => 'PPU',
                    Post::RM_SIS => 'SIS',
                )
            ))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('user', 'entity', array('class' => 'AppBundle\Entity\User'))
            ->add('category', 'entity', array('class' => 'AppBundle\Entity\Category'))
            ->add('title')
            ->add('content')
            ->add('teaser_content')
            ->add('status')
            ->add('price')
            ->add('published_at')
            ->add('revenue_model')
        ;
    }
}
