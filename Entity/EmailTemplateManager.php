<?php

namespace Rj\EmailBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Rj\EmailBundle\Entity\EmailTemplate;
use Rj\EmailBundle\Swift\Message;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EmailTemplateManager
{
    protected $em;
    protected $class;
    protected $repository;
    protected $container;
    protected $cache;

    public function __construct(EntityManager $em, $class, ContainerInterface $container)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $this->class = $em->getClassMetadata($class);
        $this->container = $container;

        $this->cache = array();
    }

    public function findTemplateByName($name)
    {
        return $this->repository->findOneBy(array('name' => $name));
    }

    public function renderTemplate($templateName, $locale, $part, $vars)
    {
        $name = "email_template:$templateName:$locale:$part";

        $vars['locale'] = $locale;

        return $this->container->get('twig')->render($name, $vars);
    }

    public function renderEmail($templateName, $locale = null, $vars = array(), Message $message = null)
    {
        if (!$locale) {
            $locale = $this->container->getParameter('rj_email.default_locale');
        }
        if (!$template = $this->getTemplate($templateName)) {
            throw new \RuntimeException(sprintf("Email template %s doesn't exist", $templateName));
        }

        if ($message && $id = $message->getUniqueId()) {
            $vars['unique_id'] = $id;
        }

        $tr = $this->getTemplateTranslation($template, $locale);

        $subject = $this->renderTemplate(
            $templateName
            , $locale
            , 'subject'
            , $vars
        );

        $body = $this->renderTemplate(
            $templateName
            , $locale
            , 'body'
            , $vars
        );

        return array(
            'subject'   => $subject,
            'body'      => $body,
        );
    }

    public function getTemplate($name)
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $template = $this->findTemplateByName($name);

        return $this->cache[$name] = $template;
    }

    public function getTemplateTranslation(EmailTemplate $template, $locale)
    {
        list($lang, ) = explode('_', $locale);

        return $template->translate($lang);
    }

    public function toMessage(EmailTemplate $template, $to = null)
    {
        $message = new Message();
        $message
            ->setSubject($template->getSubject())
            ->setFrom(array($template->getFromEmails() => $template->getFromName()))
            ->setBody($template->getBody())
            ;

        if ($to) {
            $message->setTo($to);
        }

        return $message;
    }
}
