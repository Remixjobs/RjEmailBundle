<?php

namespace Rj\EmailBundle\Swift;

class Message extends \Swift_Message
{
    protected $uniqueId;

    public function __construct($subject = null, $body = null, $contentType = null, $charset = null)
    {
        parent::__construct($subject, $body, $contentType, $charset);
        $this->uniqueId = $this->generateUniqueId();
    }

    public static function newInstance($subject = null, $body = null, $contentType = null, $charset = null)
    {
        return new static($subject, $body, $contentType, $charset);
    }

    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    public static function fromArray($parameters)
    {
        $message = new static();
        if (isset($parameters['subject'])) {
            $message->setSubject($parameters['subject']);
        }
        if (isset($parameters['body'])) {
            $message->setBody($parameters['body']);
        }
        if (isset($parameters['fromEmail'])) {
            if (isset($parameters['fromName'])) {
                $message->setFrom(array(
                    $parameters['fromEmail'] => $parameters['fromName']
                ));
            } else {
                $message->setFrom($parameters['fromEmail']);
            }
        }
        if (isset($parameters['contentType'])) {
            $message->setContentType($parameters['contentType']);
        }
        if (isset($parameters['charset'])) {
            $message->setCharset($parameters['charset']);
        }
        return $message;
    }

    protected function generateUniqueId()
    {
        return bin2hex(pack('d', microtime(true))); //. Random::generateToken();
    }
}
