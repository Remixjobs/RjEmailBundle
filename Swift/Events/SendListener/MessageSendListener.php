<?php

namespace Rj\EmailBundle\Swift\Events\SendListener;

use Rj\EmailBundle\Entity\SentEmailManager;
use Rj\EmailBundle\Swift\Message;

class MessageSendListener implements \Swift_Events_SendListener
{
    protected $manager;
    protected $sentUniqueIds;

    public function __construct(SentEmailManager $manager)
    {
        $this->manager = $manager;
        $this->sentUniqueIds = array();
    }

    /**
     * Invoked immediately before the Message is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function beforeSendPerformed(\Swift_Events_SendEvent $evt)
    {
    }

    /**
     * Invoked immediately after the Message is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function sendPerformed(\Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();

        if (!$message instanceof Message) {
            return;
        }

        $id = $message->getUniqueId();

        // The sendPerformed event may be triggered multiple times by
        // multiple transports (e.g. the Spool and then the real transport)

        if (isset($this->sentUniqueIds[$id])) {
            return;
        }

        $this->sentUniqueIds[$id] = true;

        $sentEmail = $this->manager->createSentEmail($message);
        $this->manager->updateSentEmail($sentEmail);
        $this->manager->detachSentEmail($sentEmail);
    }
}
