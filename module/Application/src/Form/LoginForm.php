<?php

namespace Application\Form;

use Zend\Filter\StringTrim;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Password;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class LoginForm extends Form implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('form-login');

        $this->add([
            'name'       => 'identity',
            'type'       => Text::class,
            'attributes' => [
                'placeholder' => 'Email',
                'autofocus'   => 'autofocus',
            ],
        ]);

        $this->add([
            'name'       => 'credential',
            'type'       => Password::class,
            'attributes' => [
                'placeholder' => 'Password',
            ],
        ]);

        $this->add([
            'name' => 'token',
            'type' => Csrf::class,
        ]);

        $this->add([
            'name'    => 'submit',
            'type'    => Submit::class,
            'options' => [
                'label' => 'Sign in',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getInputFilterSpecification()
    {
        return [
            [
                'name'     => 'identity',
                'required' => true,
                'filters'  => [
                    ['name' => StringTrim::class],
                ],
            ],
            [
                'name'     => 'credential',
                'required' => true,
            ],
        ];
    }
}
