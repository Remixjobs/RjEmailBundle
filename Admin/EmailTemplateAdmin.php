<?php

namespace Rj\EmailBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\Admin;
use Rj\EmailBundle\Form\Type\CallbackType;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Route\RouteCollection;

class EmailTemplateAdmin extends Admin
{
    protected $baseRouteName = 'email_template';
    protected $baseRoutePattern = 'email_template';
    protected $locales;

    public function setLocales(array $locales)
    {
        $this->locales = $locales;
    }

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
        $formMapper
            ->with('Email Templates')
                ->add('name')
            ->end()
            ;

        $locales = $this->locales;

        foreach ($locales as $locale) {
            $formMapper
                ->with(sprintf("Subject", $locale))
                    ->add(sprintf("translationProxies_%s_subject", $locale), 'text', array(
                        'label' => $locale,
                        'property_path' => sprintf('translationProxies[%s].subject', $locale),
                    ))
                ->end()
                ;
        }

        foreach ($locales as $locale) {
            $formMapper
                ->with(sprintf("Body", $locale))
                    ->add(sprintf("translationProxies_%s_body", $locale), 'textarea', array(
                        'label' => $locale,
                        'property_path' => sprintf('translationProxies[%s].body', $locale),
                    ))
                ->end()
                ;
        }

        foreach ($locales as $locale) {
            $formMapper
                ->with(sprintf("From name", $locale))
                    ->add(sprintf("translationProxies_%s_fromName", $locale), 'text', array(
                        'label' => $locale,
                        'property_path' => sprintf('translationProxies[%s].fromName', $locale),
                        'required' => false,
                    ))
                ->end()
                ;
        }

        foreach ($locales as $locale) {
            $formMapper
                ->with(sprintf("From email", $locale))
                    ->add(sprintf("translationProxies_%s_fromEmail", $locale), 'email', array(
                        'label' => $locale,
                        'property_path' => sprintf('translationProxies[%s].fromEmail', $locale),
                        'required' => false,
                    ))
                ->end()
                ;
        }
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

    protected function configureSideMenu(MenuItemInterface $menu, $action, Admin $childAdmin = null)
    {
        if ('edit' == $action) {
            $item = $this->menuFactory->createItem('send_test', array(
                'uri' => 'javascript:void(send_test())',
                'label' => 'Send test email',
            ));
            $menu->addChild($item);
        }
    }

    public function setTemplates(array $templates)
    {
        parent::setTemplates($templates);
        $this->setTemplate('edit', 'RjEmailBundle:EmailTemplate:edit.html.twig');
    }

    public function configureRoutes(RouteCollection $collection)
    {
        $collection->add('send_test', $this->getRouterIdParameter().'/send_test');
    }
}
