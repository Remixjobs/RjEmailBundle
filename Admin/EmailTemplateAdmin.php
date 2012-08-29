<?php

namespace Rj\EmailBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\Admin;
use Rj\EmailBundle\Form\Type\CallbackType;

class EmailTemplateAdmin extends Admin
{
    protected $baseRouteName = 'email_template';
    protected $baseRoutePattern = 'email_template';

    //show
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }

    //add
    protected function configureFormFields(FormMapper $formMapper)
    {
        $type = new CallbackType(function($builder) {
            $builder->add('subject', 'text', array(
                'label' => 'Subject'
            ));
            $builder->add('body', 'textarea', array(
                'label' => 'Body'
            ));
        });

        $formMapper
            ->with('Email Templates')
                ->add('name')
                ->add('translationProxies', 'collection', array(
                    'type' => $type
                ))
            ->end()
        ;
    }

    //list
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('name')
            ->addIdentifier('createdAt')
            ->addIdentifier('updatedAt')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'view' => array(),
                    'edit' => array(),
                    'delete' => array(),
                    //'send' => array(),
                )
            ))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('createdAt')
            ->add('updatedAt')
            ;
    }
}
