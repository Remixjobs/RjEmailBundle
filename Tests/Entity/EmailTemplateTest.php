<?php

namespace Rj\EmailBundle\Tests\Entity;

use Rj\EmailBundle\Entity\EmailTemplate;

class EmailTemplateTest extends \PHPUnit_Framework_TestCase
{
    public function testEmailTemplateTranslation()
    {
        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName('title');

        //subject
        $emailTemplate->translate('en')->setSubject('subject');
        $emailTemplate->translate('fr')->setSubject('sujet');
        $this->assertEquals($emailTemplate->translate('en')->getSubject(), 'subject');
        $this->assertEquals($emailTemplate->translate('fr')->getSubject(), 'sujet');

        //body
        $emailTemplate->translate('en')->setBody('body');
        $emailTemplate->translate('fr')->setBody('corps');
        $this->assertEquals($emailTemplate->translate('en')->getBody(), 'body');
        $this->assertEquals($emailTemplate->translate('fr')->getBody(), 'corps');

        //fromEmail
        $emailTemplate->translate('en')->setFromEmail('jeremy+en@emailbundle.com');
        $emailTemplate->translate('fr')->setFromEmail('jeremy+fr@emailbundle.fr');
        $this->assertEquals($emailTemplate->translate('en')->getFromEmail(), 'jeremy+en@emailbundle.com');
        $this->assertEquals($emailTemplate->translate('fr')->getFromEmail(), 'jeremy+fr@emailbundle.fr');

        //fromName
        $emailTemplate->translate('en')->setFromName('Jeremy en');
        $emailTemplate->translate('fr')->setFromName('Jeremy fr');
        $this->assertEquals($emailTemplate->translate('en')->getFromName(), 'Jeremy en');
        $this->assertEquals($emailTemplate->translate('fr')->getFromName(), 'Jeremy fr');
    }
}
