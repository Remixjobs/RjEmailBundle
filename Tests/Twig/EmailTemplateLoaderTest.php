<?php

use Rj\EmailBundle\Twig\EmailTemplateLoader;
use Rj\EmailBundle\Entity\EmailTemplate;
use Rj\EmailBundle\Entity\EmailTemplateTranslation;

class EmailTemplateLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $manager;
    protected $parent;

    public function setUp()
    {
        $this->manager = $this->getMockBuilder('Rj\EmailBundle\Entity\EmailTemplateManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->parent = $this->getMockBuilder('\Twig_LoaderInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     */
    public function shouldGenerateExceptionWithInvalidTemplateName()
    {
        $loader = new EmailTemplateLoader($this->parent, $this->manager);
        $source = $loader->getSource('invalid template');
        //* @test
        //* @expectedException Exception
        //* @expectedExceptionMessage invalid template name
    }

    /**
     * @test
     * @expectedException Twig_Error_Loader
     * @expectedExceptionMessage Unable to find email template name
     */
    public function shouldGenerateTwigException()
    {
        $loader = new EmailTemplateLoader($this->parent, $this->manager);
        $source = $loader->getSource('email_template:name:fr_FR:body');
    }

    /**
     * @test
     * @expectedException Twig_Error_Loader
     * @expectedExceptionMessage Invalid template part invalid
     */
    public function shouldGenerateInvalidTemplateException()
    {
        $template = new EmailTemplate;
        $template->setName('name');
        $template->translate('fr')->setBody('body');

        $this->manager->expects($this->once())
            ->method('getTemplate')
            ->will($this->returnValue($template))
            ;

        $this->manager->expects($this->once())
            ->method('getTemplateTranslation')
            ->will($this->returnValue($template->translate('fr')))
            ;


        $loader = new EmailTemplateLoader($this->parent, $this->manager);
        $source = $loader->getSource('email_template:name:fr_FR:invalid');
    }

    /**
     * @test
     */
    public function shouldGetSource()
    {
        $template = new EmailTemplate;
        $template->setName('name');
        $template->translate('fr')->setBody('body');

        $this->manager->expects($this->once())
            ->method('getTemplate')
            ->will($this->returnValue($template))
            ;

        $this->manager->expects($this->once())
            ->method('getTemplateTranslation')
            ->will($this->returnValue($template->translate('fr')))
            ;


        $loader = new EmailTemplateLoader($this->parent, $this->manager);
        $source = $loader->getSource('email_template:name:fr_FR:body');
        $this->assertEquals($source, 'body');
    }

    public function testGetCacheKey()
    {}

    public function testIsFresh()
    {}
}
