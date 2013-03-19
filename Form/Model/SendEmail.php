<?php
namespace Rj\EmailBundle\Form\Model;

use Rj\EmailBundle\Entity\EmailTemplate;
use Symfony\Component\Validator\Constraints as Assert;

class SendEmail
{
    /**
     * @var string
     * 
     * @Assert\NotBlank
     */
    public $locale;

    /**
     * @var EmailTemplate
     * 
     * @Assert\NotBlank
     */
    public $template;

    /**
     * @var array
     */
    public $subjectVars;

    /**
     * @var array
     */
    public $bodyVars;

    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $fromName;
    
    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Email
     */
    public $fromEmail;

    /**
     * @var string
     * 
     * @Assert\NotBlank
     * @Assert\Email
     */
    public $toEmail;

    /**
     * @var boolean
     * 
     * @Assert\NotBlank
     */
    public $confirmSend;
}