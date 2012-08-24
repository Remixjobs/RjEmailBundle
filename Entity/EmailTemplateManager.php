<?php

namespace Rj\EmailBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Rj\EmailBundle\Entity\EmailTemplate;
use Rj\EmailBundle\Swift\Message;

class EmailTemplateManager
{
    protected $em;
    protected $class;
    protected $repository;
    protected $twig;
    protected $cache;

    public function __construct(EntityManager $em, $class, \Twig_Environment $twig)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $this->class = $em->getClassMetadata($class);
        $this->twig = $twig;

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

        return $this->twig->render($name, $vars);
    }

    public function renderEmail($templateName, $locale, $vars, Message $message = null)
    {
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
            'fromName'  => $tr->getFromName(),
            'fromEmail' => $tr->getFromEmail(),
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
}
