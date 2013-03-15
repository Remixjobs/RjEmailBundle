<?php
namespace Rj\EmailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Util\PropertyPath;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SendEmailType extends AbstractType
{
    protected $emailTemplateClass;
    
    protected $emailTemplateRepository;
    
    protected $locales;
    
    public function __construct(ObjectManager $objectManager, $emailTemplateClass, array $locales)
    {
        $this->emailTemplateClass = $emailTemplateClass;
        $this->emailTemplateRepository = $objectManager->getRepository($emailTemplateClass);
        
        $this->locales = array();
        foreach ($locales as $locale) {
            $this->locales[$locale] = \Locale::getDisplayLanguage($locale);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formFactory = $builder->getFormFactory();
        
        $builder->add('locale', 'choice', array(
            'choices' => $this->locales,
            'label' => 'Language'
        ));

        $builder->addEventListener(FormEvents::PRE_BIND, function(FormEvent $event) use ($formFactory) {
            $data = $event->getData();
            $form = $event->getForm();
            if (isset($data['locale'])) {
                $form->add($formFactory->createNamed('template', 'entity', null, array(
                    'class' => 'Rj\EmailBundle\Entity\EmailTemplate',
                    'property' => 'name',
                    'empty_value' => 'Select Template...',
                    'query_builder' => function(EntityRepository $emailTemplateRepository) use ($data) {
                        return $emailTemplateRepository->createQueryBuilder('et')
                            ->addSelect('ett')
                            ->innerJoin('et.translations', 'ett')
                            ->andWhere('ett.locale = :locale')
                            ->setParameter('locale', $data['locale'])
                            ;
                    },
                )));
            }

            if (isset($data['template']) && $template = $this->emailTemplateRepository->find($data['template'])) {
                $translatedTemplate = $template->translate($data['locale']);
                
                $form->add($formFactory->createNamed('subject', 'textarea', null, array(
                    'read_only' => true,
                    'virtual' => true,
                )));
                $data['subject'] = $translatedTemplate->getSubject();
                
                $matches = array();
                if (preg_match_all('/\{\{\s*([^\}\s]+)\s*\}\}/', $translatedTemplate->getSubject(), $matches)) {
                    $vars = array();
                    foreach ($matches[1] as $match) {
                        $vars[str_replace('.' , ':', $match)] = '';
                    }

                    $form->add($formFactory->createNamed('subject_vars', 'collection', null, array(
                        'data' => $vars,
                        'type' => 'text',
                        'allow_add' => false,
                        'allow_delete' => false,
                        'property_path' => 'subjectVars'
                    )));
                    
                    $form->get('subject_vars')->addError(new FormError(''));
                }

                $form->add($formFactory->createNamed('body', 'textarea', $translatedTemplate->getBody(), array(
                    'read_only' => true,
                    'virtual' => true, 
                )));
                $data['body'] = $translatedTemplate->getBody();

                $matches = array();
                if (preg_match_all('/\{\{\s*([^\}\s]+)\s*\}\}/', $translatedTemplate->getBody(), $matches)) {
                    $vars = array();
                    foreach ($matches[1] as $match) {
                        $vars[str_replace('.' , ':', $match)] = '';
                    }

                    $form->add($formFactory->createNamed('body_vars', 'collection', null, array(
                        'data' => $vars,
                        'type' => 'text',
                        'allow_add' => false,
                        'allow_delete' => false,
                        'property_path' => 'bodyVars'
                    )));

                    $form->get('body_vars')->addError(new FormError(''));
                }

                $form->add($formFactory->createNamed('fromName', 'text'));
                $data['fromName'] = $translatedTemplate->getFromName();

                $form->add($formFactory->createNamed('fromEmail', 'email'));
                $data['fromEmail'] = $translatedTemplate->getFromEmail();

                $form->add($formFactory->createNamed('toEmail', 'email'));
                $data['toEmail'] = $translatedTemplate->getFromEmail();

                $form->add($formFactory->createNamed('confirmSend', 'checkbox'));
            }
                
            $event->setData($data);
        });

        $builder->addEventListener(FormEvents::POST_BIND, function(FormEvent $event) use ($formFactory) {
            //TODO pars variables
        });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Rj\EmailBundle\Form\Model\SendEmail',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rj_email_send_email';
    }
}