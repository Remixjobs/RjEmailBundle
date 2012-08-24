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

    protected function generateUniqueId()
    {
        return bin2hex(pack('d', microtime(true))); //. Random::generateToken();
    }
}
