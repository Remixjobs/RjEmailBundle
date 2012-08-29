<?php

namespace Rj\EmailBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Rj\EmailBundle\Entity\EmailTemplate;
use Rj\EmailBundle\Entity\EmailTemplateTranslation;
use Rj\EmailBundle\Entity\EmailTemplateManager;

class EmailTemplateLoader implements \Twig_LoaderInterface
{
    private $manager;

    public function __construct(EmailTemplateManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Gets the source code of a template, given its name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The template source code
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function getSource($name)
    {
        list($name, $locale, $part) = $this->parse($name);

        $template = $this->getTemplate($name);
        $source = $this->getTemplatePart($template, $locale, $part);

        return $source;
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The cache key
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function getCacheKey($fullName)
    {
        list($name, ) = $this->parse($fullName);

        $template = $this->getTemplate($name);

        return
            __CLASS__
            . '#' . $fullName
            // force reload even if Twig has autoReload to false
            . '#' . $template->getUpdatedAt()->getTimestamp();
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param string    $name The template name
     * @param timestamp $time The last modification time of the cached template
     *
     * @return Boolean true if the template is fresh, false otherwise
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function isFresh($name, $time)
    {
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
            throw new \Twig_Error_Loader('invalid template name');
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
