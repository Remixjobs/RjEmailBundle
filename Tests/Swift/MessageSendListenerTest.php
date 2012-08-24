<?php

use Rj\EmailBundle\Swift\Events\SendListener\MessageSendListener;
use Rj\EmailBundle\Entity\SentEmailManager;
use Rj\EmailBundle\Swift\Message;
use Rj\EmailBundle\Entity\SentEmail;

class MessageSendListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $em;
    protected $repository;
    protected $manager;

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

        $this->manager = new SentEmailManager($this->em, $this->repository);
    }

    /**
     * @test
     */
    public function testSendPerformed()
    {
        $message = new Message;
        $message
            ->setFrom(array('jeremy@test.com' => 'Jeremy'))
            ->setTo(array('jeremy@test.com' => 'Jeremy'))
            ->setSubject('subject')
            ->setBody('body')
            ->generateId()
            ;

        $transport = $this->_createTransport();
        $evt = $this->_createSendEvent($transport);
        $evt->expects($this->any())
            ->method('getMessage')
            ->will($this->returnValue($message))
            ;

        //test the manager is called only once
        $manager = $this->getMockBuilder('Rj\EmailBundle\Entity\SentEmailManager')
            ->disableOriginalConstructor()
            ->getMock();

        $sentMessage = SentEmail::fromMessage($message);
        $manager->expects($this->once())
            ->method('createSentEmail')
            ->will($this->returnValue($sentMessage))
            ;

        $plugin = new MessageSendListener($manager);
        $plugin->sendPerformed($evt);
        $plugin->sendPerformed($evt);
    }

    private function _createTransport()
    {
        return $this->getMock('\Swift_Transport');
    }

    private function _createSendEvent($transport)
    {
        return $this->getMockBuilder('\Swift_Events_SendEvent')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function _createSleeper()
    {
        return $this->getMock('\Swift_Plugins_Sleeper');
    }

}
