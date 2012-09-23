<?php

namespace Rj\EmailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CallbackType extends AbstractType
{
    protected $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        call_user_func($this->callback, $builder, $options);
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => null,
        );
    }

    public function getName()
    {
        return 'rjemail_callback';
    }
}
