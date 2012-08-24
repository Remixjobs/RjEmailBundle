<?php

namespace Rj\EmailBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Rj\EmailBundle\Entity\EmailTemplate;
use Rj\EmailBundle\Entity\EmailTemplateTranslation;
use Rj\EmailBundle\Entity\EmailTemplateManager;

class EmailTemplateLoader implements \Twig_LoaderInterface
{
    private $parent;
    private $manager;

    public function __construct(\Twig_LoaderInterface $parent, EmailTemplateManager $manager)
    {
        $this->parent = $parent;
        $this->manager = $manager;
    }

    public function getSource($name)
    {
        if (!$this->canHandle($name)) {
            return $this->parent->getSource($name);
        }

        list($name, $locale, $part) = $this->parse($name);

        $template = $this->getTemplate($name);
        $source = $this->getTemplatePart($template, $locale, $part);

        return $source;
    }

    public function getCacheKey($fullName)
    {
        if (!$this->canHandle($fullName)) {
            return $this->parent->getCacheKey($fullName);
        }

        list($name, ) = $this->parse($fullName);

        $template = $this->getTemplate($name);

        return
            __CLASS__
            . '#' . $fullName
            // force reload even if Twig has autoReload to false
            . '#' . $template->getUpdatedAt()->getTimestamp();
    }

    public function isFresh($name, $time)
    {
        if (!$this->canHandle($name)) {
            return $this->parent->isFresh($name, $time);
        }

        list($name, ) = $this->parse($name);

        $template = $this->getTemplate($name);

        return $template->getUpdatedAt()->getTimestamp() <= $time;
    }

    private function canHandle($name)
    {
        return 0 === strpos($name, 'email_template:');
    }

    private function parse($name)
    {
        if (!preg_match('#^email_template:([^:]+):([^:]+):([^:]+)$#', $name, $m)) {
            throw new \Exception('invalid template name');
        }

        return array($m[1], $m[2], $m[3]);
    }

    private function getTemplate($name)
    {
        if (!$template = $this->manager->getTemplate($name)) {
            throw new \Twig_Error_Loader(sprintf("Unable to find email template %s", $name));
        }

        return $template;
    }

    private function getTemplateTranslation(EmailTemplate $template, $locale)
    {
        return $this->manager->getTemplateTranslation($template, $locale);
    }

    private function getTemplatePart(EmailTemplate $template, $locale, $part)
    {
        $translation = $this->getTemplateTranslation($template, $locale);
        if (!$translation) {
            throw new \Exception(sprintf('No translation %s for %s', $locale, $template->getName()));
        }

        switch ($part) {
        case 'subject':
            return '{% autoescape false %}' . $translation->getSubject() . '{% endautoescape %}';
        case 'body':
            return $translation->getBody();
        default:
            throw new \Twig_Error_Loader(sprintf("Invalid template part %s", $part));
        }
    }
}
