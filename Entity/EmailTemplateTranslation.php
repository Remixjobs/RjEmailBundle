<?php

namespace Rj\EmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translator\Entity\Translation;
use Gedmo\Mapping\Annotation\Loggable;
use Gedmo\Mapping\Annotation\Versioned;

/**
 * @ORM\Table(
 *  name="email_translation",
 *  indexes={@ORM\index(name="lookup_idx", columns={
 *   "locale", "translatable_id"
 *  })}
 *  ,uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *   "locale", "translatable_id", "property"
 *  })}
 * )
 * @ORM\Entity
 * @Loggable
 */
class EmailTemplateTranslation extends Translation
{
    /**
     * @ORM\ManyToOne(targetEntity="EmailTemplate", inversedBy="translations")
     */
    protected $translatable;

    /**
     * @var string $locale
     *
     * @ORM\Column(type="string", length=8)
     * @Versioned
     */
    protected $locale;

    /**
     * @var string $property
     *
     * @ORM\Column(type="string", length=32)
     * @Versioned
     */
    protected $property;

    /**
     * @var text $value
     *
     * @ORM\Column(type="text", nullable=true)
     * @Versioned
     */
    protected $value;
}
