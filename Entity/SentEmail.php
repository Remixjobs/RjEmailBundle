<?php

namespace Rj\EmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use FOS\OAuthServerBundle\Util\Random;
use Rj\EmailBundle\Swift\Message;

/**
 * Rj\MailBundle\Entity\SentEmail
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class SentEmail
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $uniqueId;

    /**
     * @var string $fromEmails
     *
     * @ORM\Column(name="fromEmails", type="array")
     */
    private $fromEmails;

    /**
     * @var string $toEmails
     *
     * @ORM\Column(name="toEmails", type="array")
     */
    private $toEmails;

    /**
     * @var string $subject
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

    /**
     * @var text $body
     *
     * @ORM\Column(name="body", type="text")
     */
    private $body;

    /**
     * @var text $source
     *
     * @ORM\Column(name="source", type="text")
     */
    private $source;

    /**
     * @var string $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var string $contentType
     *
     * @ORM\Column(name="contentType", type="string", length=255)
     */
    private $contentType;

    public function __construct()
    {
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setUniqueId($id)
    {
        $this->uniqueId = $id;
    }

    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
     * Set fromEmails
     *
     * @param string $fromEmails
     * @return SentEmail
     */
    public function setFromEmails($fromEmails)
    {
        $this->fromEmails = $fromEmails;
        return $this;
    }

    /**
     * Get fromEmails
     *
     * @return string
     */
    public function getFromEmails()
    {
        return $this->fromEmails;
    }

    /**
     * Set toEmails
     *
     * @param string $toEmails
     * @return SentEmail
     */
    public function setToEmails($toEmails)
    {
        $this->toEmails = $toEmails;
        return $this;
    }

    /**
     * Get toEmails
     *
     * @return string
     */
    public function getToEmails()
    {
        return $this->toEmails;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return SentEmail
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set body
     *
     * @param text $body
     * @return SentEmail
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Get body
     *
     * @return text
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set source
     *
     * @param text $source
     * @return SentEmail
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Get source
     *
     * @return text
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set createdAt
     *
     * @param string $createdAt
     * @return SentEmail
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set contentType
     *
     * @param string $contentType
     * @return SentEmail
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * Get contentType
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    static public function fromMessage(Message $message)
    {
        $email = new static;
        $email->setUniqueId($message->getUniqueId());
        $email->setFromEmails($message->getFrom());
        $email->setToEmails($message->getTo());
        $email->setSubject($message->getSubject());
        $email->setBody($message->getBody());
        $email->setContentType($message->getContentType());
        $email->setSource($message->toString());

        return $email;
    }
}
