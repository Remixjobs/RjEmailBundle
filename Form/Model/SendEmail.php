<?php
namespace Rj\EmailBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class SendEmail
{
    /**
     * @Assert\NotBlank
     */
    public $locale;

    /**
     * @Assert\NotBlank
     */
    public $template;

    /**
     * @Assert\NotBlank
     */
    public $subjectVars;

    /**
     * @Assert\NotBlank
     */
    public $bodyVars;

    /**
     * @Assert\NotBlank
     * @Assert\Email
     */
    public $toEmail;
    
    /**
     * @Assert\NotBlank
     */
    public $fromName;

    /**
     * @Assert\NotBlank
     * @Assert\Email
     */
    public $fromEmail;

    /**
     * @Assert\NotBlank
     */
    public $confirmSend;
}