<?php

namespace Rj\EmailBundle\Entity;

use Gedmo\Translator\TranslationProxy;
use Rj\EmailBundle\Validator\TwigTemplate;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class EmailTemplateTranslationProxy extends TranslationProxy
{
    /**
     * @NotBlank
     */
    public function getSubject()
    {
        return $this->getTranslatedValue('subject');
    }

    public function setSubject($subject)
    {
        $this->setTranslatedValue('subject', $subject);

        return $this;
    }

    /**
     * @NotBlank
     */
    public function getBody()
    {
        return $this->getTranslatedValue('body');
    }

    public function setBody($body)
    {
        $this->setTranslatedValue('body', $body);

        return $this;
    }

    public function setFromEmail($fromEmail)
    {
        return $this->setTranslatedValue('fromEmail', $fromEmail);
    }

    /**
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

    public function getFromName()
    {
        return $this->getTranslatedValue('fromName');
    }
}
