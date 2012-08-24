<?php

namespace Rj\EmailBundle\Tests\Entity;

use Rj\EmailBundle\Entity\EmailTemplate;
use Rj\EmailBundle\Entity\EmailTemplateManager;

class EmailTemplateManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $em;
    protected $repository;
    protected $twig;

    public function setUp()
    {
        if (!class_exists('Doctrine\\ORM\\EntityManager')) {
            $this->markTestSkipped('Doctrine ORM not installed');
        }

        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->twig = $this->getMockBuilder('\Twig_Environment')
            ->getMock();

        $this->em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->repository));
    }

    public function testFindTemplateByName()
    {
        $emailTemplate = $this->getMock('FOS\EmailBundle\Entity\EmailTemplate');

        $criteria = array('name' => 'test');
        $this->repository->expects($this->once())
                ->method('findOneBy')
                ->with($criteria)
                ->will($this->returnValue($emailTemplate));

        $manager = new EmailTemplateManager($this->em, $this->repository, $this->twig);
        $result = $manager->findTemplateByName('test');

        $this->assertEquals($result, $emailTemplate);
    }

    public function testRenderTemplate()
    {
        $locale = "en_US";
        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName('test');
        $emailTemplate->translate('en')->setBody("Hello {#name}");
        $this->em->persist($emailTemplate);
        $this->em->flush();

        $manager = new EmailTemplateManager($this->em, $this->repository, $this->twig);
        $html = $manager->renderTemplate('test', $locale, 'body', array('name' => 'Jeremy'));
        //$this->assertTrue(is_array($html));
        //$this->assertEquals($html->getBody(), 'Hello Jeremy');
    }

    public function testGetTemplate()
    {
        $emailTemplate = $this->getMock('FOS\EmailBundle\Entity\EmailTemplate');

        $criteria = array('name' => 'template_name');
        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($criteria)
            ->will($this->returnValue($emailTemplate));

        $manager = new EmailTemplateManager($this->em, $this->repository, $this->twig);
        $result = $manager->getTemplate('template_name');

        $this->assertEquals($result, $emailTemplate);
    }

    public function testGetTemplateTranslation()
    {
        $locale = "fr_FR";
        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName('test');
        $emailTemplate->translate('fr')->setBody("Bonjour");

        $manager = new EmailTemplateManager($this->em, $this->repository, $this->twig);
        $result = $manager->getTemplateTranslation($emailTemplate, $locale);
        $this->assertEquals($result->getBody(), "Bonjour");
    }
}
