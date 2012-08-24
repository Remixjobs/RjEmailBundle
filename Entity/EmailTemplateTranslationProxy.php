<?php

namespace Rj\EmailBundle\Entity;

use Gedmo\Translator\TranslationProxy;
use Rj\EmailBundle\Validator\TwigTemplate;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class EmailTemplateTranslationProxy extends TranslationProxy
{
    public function setSubject($subject)
    {
        return $this->setTranslatedValue('subject', $subject);
    }

    /**
     * @NotBlank
     */
     //@TwigTemplate
    public function getSubject()
    {
        return $this->getTranslatedValue('subject');
    }

    public function setBody($body)
    {
        return $this->setTranslatedValue('body', $body);
    }

    /**
     * @NotBlank
     */
     //* @TwigTemplate
    public function getBody()
    {
        return $this->getTranslatedValue('body');
    }

    public function setFromEmail($fromEmail)
    {
        return $this->setTranslatedValue('fromEmail', $fromEmail);
    }

    /**
     * @NotBlank
     * @Email
     */
    public function getFromEmail()
    {
        return $this->getTranslatedValue('fromEmail');
    }

    public function setFromName($fromName)
    {
        return $this->setTranslatedValue('fromName', $fromName);
    }

    /**
     * @NotBlank
     */
    public function getFromName()
    {
        return $this->getTranslatedValue('fromName');
    }
}
