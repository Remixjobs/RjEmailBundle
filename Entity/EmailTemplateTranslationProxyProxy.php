<?php

namespace Rj\EmailBundle\Entity;

class EmailTemplateTranslationProxyProxy implements \ArrayAccess
{
    private $emailTemplate;

    public function __construct(EmailTemplate $emailTemplate)
    {
        $this->emailTemplate = $emailTemplate;
    }

    public function offsetGet($locale)
    {
        return $this->emailTemplate->translate($locale);
    }

    public function offsetSet($locale, $value)
    {
    }

    public function offsetExists($locale)
    {
        return true;
    }

    public function offsetUnset($locale)
    {
    }
}

