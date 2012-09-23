<?php

namespace Rj\EmailBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Rj\EmailBundle\Swift\Message;
use FOS\RestBundle\Controller\Annotations\View;
use Rj\EmailBundle\Entity\EmailTemplate;

class EmailTemplateAdminController extends CRUDController
{
    public function sendTestAction()
    {
        $form = $this->get('form.factory')
            ->createNamedBuilder('send_test', 'form')
            ->add('variables', 'textarea')
            ->add('to', 'textarea')
            ->getForm()
            ;

        $request = $this->get('request');

        if ('POST' === $request->getMethod()) {

            $form->bind($request->request->get($form->getName()));

            try {
                $template = $this->admin->getNewInstance();

                $this->admin->setSubject($template);

                $templateForm = $this->admin->getForm();
                $templateForm->setData($template);

                $templateForm->bind($request->request->get($templateForm->getName()));

                $data = $form->getData();

                $vars = json_decode($data['variables'], true);

                if (null === $vars) {
                    return $this->renderJson(array(
                        'error' => "variables must be a valid json string",
                    ), 400);
                }

                $tos = preg_split("#\s+#", $data['to'], -1, PREG_SPLIT_NO_EMPTY);

                $languages = $this->container->getParameter('rj_email.locales');

                foreach ($tos as $to) {
                    foreach ($languages as $language) {
                        try {
                            $this->send($template, $language, $vars, $to);
                        } catch(\Exception $e) {
                            return $this->renderJson(array(
                                'error' => sprintf("An error occured while sending with language=%s; to=%s: %s", $language, $to, $e->getMessage()),
                            ), 400);
                        }
                    }
                }

                return $this->renderJson(array(
                    'success' => true,
                ));

            } catch(\Exception $e) {

                return $this->renderJson(array(
                    'error' => $e->getMessage(),
                ), 400);
            }
        } else {

            return $this->renderJson(array(
                'html' => $this->renderView('RjEmailBundle:EmailTemplate:sendTest.html.twig', array(
                    'form' => $form->createView(),
                )),
            ));
        }
    }

    protected function send(EmailTemplate $template, $language, $vars, $to)
    {
        $manager = $this->get('rj_email.email_template_manager');

        $message = new Message;

        $ret = $manager->renderFromEmailTemplate($template, $language, $vars, $message);

        $message
            ->setFrom($ret['fromEmail'], $ret['fromName'])
            ->setTo($to)
            ->setSubject($ret['subject'])
            ->setBody($ret['body'], 'text/html', 'utf-8')
            ;

        $this->get('mailer')->send($message);
    }
}
