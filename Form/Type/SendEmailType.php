<?php
namespace Rj\EmailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Util\PropertyPath;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;

class SendEmailType extends AbstractType
{
    /**
     * @var string
     */
    protected $emailTemplateClass;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $emailTemplateRepository;

    /**
     * @var array
     */
    protected $locales;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;
    
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
        $this->formFactory = $builder->getFormFactory();
        
        $builder->add('locale', 'choice', array(
            'choices' => $this->locales,
            'label' => 'Language'
        ));

        $builder->addEventListener(FormEvents::PRE_BIND, array($this, 'buildTemplateField'));
        $builder->addEventListener(FormEvents::PRE_BIND, array($this, 'buildSubjectFields'));
        $builder->addEventListener(FormEvents::PRE_BIND, array($this, 'buildBodyFields'));
        $builder->addEventListener(FormEvents::PRE_BIND, array($this, 'buildEmailFields'));
        $builder->addEventListener(FormEvents::PRE_BIND, array($this, 'buildConfirmField'));
        
        $builder->addEventListener(FormEvents::BIND, array($this, 'convertSubjectVarsToArray'));
        $builder->addEventListener(FormEvents::BIND, array($this, 'convertBodyVarsToArray'));
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function buildTemplateField(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        
        if (isset($data['locale'])) {
            $form->add($this->formFactory->createNamed('template', 'entity', null, array(
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

        $event->setData($data);
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function buildSubjectFields(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        
        if (isset($data['template']) && $template = $this->emailTemplateRepository->find($data['template'])) {
            $translatedTemplate = $template->translate($data['locale']);

            $form->add($this->formFactory->createNamed('subject', 'textarea', null, array(
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

                $form->add($this->formFactory->createNamed('subject_vars', 'collection', null, array(
                    'data' => $vars,
                    'type' => 'text',
                    'allow_add' => false,
                    'allow_delete' => false,
                    'property_path' => 'subjectVars'
                )));
            }
        }
        
        $event->setData($data);
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function convertSubjectVarsToArray(FormEvent $event)
    {
        $data = $event->getData();        
        if (isset($data->subjectVars) && is_array($data->subjectVars)) {
            $subjectVars = array();
            foreach ($data->subjectVars as $rawSubjectVar => $value) {
                $propertyPath = new PropertyPath('['.str_replace(':' , '][', $rawSubjectVar).']');
                $propertyPath->setValue($subjectVars, $value);
            }

            $data->subjectVars = $subjectVars; 
        }

        $event->setData($data);
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function convertBodyVarsToArray(FormEvent $event)
    {
        $data = $event->getData();
        if (isset($data->bodyVars) && is_array($data->bodyVars)) {
            $bodyVars = array();
            foreach ($data->bodyVars as $rawSubjectVar => $value) {
                $propertyPath = new PropertyPath('['.str_replace(':' , '][', $rawSubjectVar).']');
                $propertyPath->setValue($bodyVars, $value);
            }

            $data->bodyVars = $bodyVars;
        }

        $event->setData($data);
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function buildBodyFields(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (isset($data['template']) && $template = $this->emailTemplateRepository->find($data['template'])) {
            $translatedTemplate = $template->translate($data['locale']);

            $form->add($this->formFactory->createNamed('body', 'textarea', $translatedTemplate->getBody(), array(
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

                $form->add($this->formFactory->createNamed('body_vars', 'collection', null, array(
                    'data' => $vars,
                    'type' => 'text',
                    'allow_add' => false,
                    'allow_delete' => false,
                    'property_path' => 'bodyVars'
                )));
            }
        }

        $event->setData($data);
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function buildEmailFields(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (isset($data['template']) && $template = $this->emailTemplateRepository->find($data['template'])) {
            $translatedTemplate = $template->translate($data['locale']);

            $form->add($this->formFactory->createNamed('fromName', 'text'));
            $data['fromName'] = $translatedTemplate->getFromName();

            $form->add($this->formFactory->createNamed('fromEmail', 'email'));
            $data['fromEmail'] = $translatedTemplate->getFromEmail();

            $form->add($this->formFactory->createNamed('toEmail', 'email'));
            $data['toEmail'] = $translatedTemplate->getFromEmail();
        }

        $event->setData($data);
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function buildConfirmField(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (isset($data['template']) && $template = $this->emailTemplateRepository->find($data['template'])) {
            $form->add($this->formFactory->createNamed('confirmSend', 'checkbox'));
        }

        $event->setData($data);
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