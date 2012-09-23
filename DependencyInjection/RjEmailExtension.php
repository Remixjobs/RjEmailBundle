<?php

namespace Rj\EmailBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class RjEmailExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        //var_dump($container->getParameter('session.default_locale'));
        //exit;
        $container->setParameter('rj_email.custom_loader', $config['custom_loader']);
        $container->setParameter('rj_email.default_locale', $config['default_locale']);
        $container->setParameter('rj_email.locales', $config['locales']);
        $container->setParameter('rj_email.emails', $config['emails']);
        $container->setParameter('rj_email.default_from_name', $config['default_from_name']);
        $container->setParameter('rj_email.default_from_email', $config['default_from_email']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        //todo: only if there is fosuserbundle
        $loader->load('mailer.xml');

        if (class_exists("Sonata\AdminBundle\Admin\Admin")) {
            $loader->load('sonata.xml');
        }
    }
}
