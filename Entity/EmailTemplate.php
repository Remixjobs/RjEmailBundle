<?php

namespace Rj\EmailBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo;


use Rj\EmailBundle\Entity\EmailTemplateTranslationProxy;

/**
 * @ORM\Table(
 *  name="email",
 *  indexes={@ORM\Index(name="name", columns={"name"})}
 * )
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * 
 * @Gedmo\Loggable
 */
class EmailTemplate
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", unique=true, length=255)
     * 
     * @Gedmo\Versioned
     * 
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var \DateTime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * 
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime $updatedAt
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     * 
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="EmailTemplateTranslation", mappedBy="translatable", cascade={"persist"})
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection;
        $this->contentType = 'text/html';
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * 
     * @return EmailTemplate
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function translate($locale)
    {
        return new EmailTemplateTranslationProxy($this
            , $locale
            , array('subject', 'body', 'bodyHtml')
            , __CLASS__ . 'Translation'
            , $this->translations
        );
    }

    /**
     * @Assert\Valid
     */
    public function getTranslationProxies()
    {
        return new EmailTemplateTranslationProxyProxy($this);
    }

    public function setTranslationProxies()
    {
    }

    public function getEnTranslation()
    {
        return $this->translate('en');
    }

    public function __toString()
    {
        return $this->getName();
    }
}