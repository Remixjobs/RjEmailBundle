<?php

use Rj\EmailBundle\Entity\SentEmailManager;
use Rj\EmailBundle\Swift\Message;

class SentEmailManagerTest extends \PHPUnit_Framework_TestCase
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

    public function testFindSentEmailByUniqueId()
    {
        $sentEmail = $this->getMock('Rj\Entity\SentEmail');
        $criteria = array('uniqueId' => 'uniqueid');

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($criteria)
            ->will($this->returnValue($sentEmail));

        $manager = new SentEmailManager($this->em, $this->repository);
        $return = $manager->findSentEmailByUniqueId('uniqueid');

        $this->assertEquals($sentEmail, $return);
    }

    public function testCreateSentEmail()
    {
        $message = new Message('subject', 'body', 'text/plain', 'utf-8');
        $manager = new SentEmailManager($this->em, $this->repository);
        $return = $manager->createSentEmail($message);

        $this->assertEquals($return->getSubject(), 'subject');
    }
}
