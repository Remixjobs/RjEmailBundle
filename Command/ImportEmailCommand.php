<?php

namespace Rj\EmailBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use FOS\UserBundle\Model\User;
use Rj\EmailBundle\Entity\EmailTemplate;

/**
 * @author Jeremy Marc <jeremy.marc@me.com>
 */
class ImportEmailCommand extends ContainerAwareCommand
{
	/**
	 * @see Command
	 */
	protected function configure()
	{
		$this
			->setName('rj:email:import')
			->setDescription('Import FOS Email into EmailTemplate')
			;
	}

	/**
	 * @see Command
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$emails = array('resetting', 'registration');
		$langs = array('en', 'fr', 'de');

		$container = $this->getApplication()->getKernel()->getContainer();
		$em = $container->get('doctrine')->getEntityManager();
		$translator = $container->get('translator');
		$session = $container->get('session');

		foreach($emails as $email) {
			$template = new EmailTemplate();
			$template->setName($email);

			foreach($langs as $lang) {
				$subject = $this->replaceTranslatorVariables($translator->trans($email . '.email.subject', array(), 'FOSUserBundle', $lang));
				$body = $this->replaceTranslatorVariables($translator->trans($email . '.email.message', array(), 'FOSUserBundle', $lang));

				$template->translate($lang)
					->setSubject($subject)
					->setBody($body)
					;
			}

			$em->persist($template);
			$em->flush();
		}

		$output->writeln("All email templates have been imported.");
	}

	private function replaceTranslatorVariables($str)
	{
		//todo: REGEX /%([^%]+)%/
		$str = str_replace('%username%', '{{ username }}', $str);
		$str = str_replace('%confirmationUrl%', '{{ confirmationUrl }}', $str);

		return $str;
	}
}
