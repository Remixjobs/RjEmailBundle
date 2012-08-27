<?php

namespace Rj\EmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation\Loggable;
use Gedmo\Mapping\Annotation\Versioned;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Valid;
use Rj\EmailBundle\Entity\EmailTemplateTranslationProxy;

/**
 * @ORM\Table(
 *  indexes={@ORM\Index(name="name", columns={"name"})}
 * )
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @Loggable
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
     * @ORM\Column(name="name", type="string", length=255)
     * @Versioned
     */
    private $name;

    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     * @Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="EmailTemplateTranslation", mappedBy="translatable", cascade={"persist"})
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection;
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

    /**
     * Set name
     *
     * @param string $name
     * @return EmailTemplate
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     * @return EmailTemplate
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     * @return EmailTemplate
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function translate($locale)
    {
        return new EmailTemplateTranslationProxy($this
            , $locale
            , array('subject', 'body')
            , __CLASS__ . 'Translation'
            , $this->translations
        );
    }

    /**
     * @Valid
     */
    public function getTranslationProxies()
    {
        $langs = array('de', 'en', 'fr');

        $ret = array();

        foreach ($langs as $lang) {
            $ret[$lang] = $this->translate($lang);
        }

        return $ret;
    }

    public function getEnTranslation()
    {
        return $this->translate('en');
    }

    public function setTranslationProxies()
    {
    }

    public function __toString()
    {
        return $this->getName();
    }
}
