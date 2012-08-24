<?php

namespace Rj\EmailBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Rj\EmailBundle\Entity\EmailTemplate;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//extends ContainerAwareInterface

class EmailController extends Controller
{
    public function indexAction($unique_id)
    {
        $sentEmailManager = $this->container->get('rj_email.sent_email.manager');
        $sentEmail = $sentEmailManager->findSentEmailByUniqueId($unique_id);

        if (!$sentEmail) {
            throw new \Exception('no such email'); //TODO: 404
        }

        return new Response($sentEmail->getBody(), 200, array(
            'Content-Type' => $sentEmail->getContentType(),
        ));
    }

    public function sendAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $templateName = "test";
        $locale = "fr_FR";
        $part = "body";
        $vars = array("name" => "Jeremy");

        $emailTemplateManager = $this->container->get('rj_email.email_template.manager');

        $template = new EmailTemplate;
        $template->setName('test');
        $template->translate('fr')->setBody("Hello #{name}!");
        //$em->persist($template);
        //$em->flush();

        $response = $emailTemplateManager->renderEmail($templateName, $locale, $vars);
        return new Response($response['body'], 200, array(
            'Content-Type' => "text/html"
        ));
    }
}
