<?php

namespace Rj\EmailBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Rj\EmailBundle\Entity\EmailTemplate;
use Rj\EmailBundle\Swift\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EmailController extends Controller
{
    /**
     * View an sent email online
     */
    public function indexAction($unique_id)
    {
        $sentEmailManager = $this->container->get('rj_email.sent_email_manager');
        $sentEmail = $sentEmailManager->findSentEmailByUniqueId($unique_id);

        if (!$sentEmail) {
            throw new NotFoundHttpException('This email doesn\'t exists.');
        }

        return new Response($sentEmail->getBody(), 200, array(
            'Content-Type' => $sentEmail->getContentType(),
        ));
    }
}
