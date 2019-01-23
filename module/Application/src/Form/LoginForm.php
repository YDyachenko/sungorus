<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class LoginForm extends Form implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('form-login');

        $this->add([
            'name'       => 'identity',
            'type'       => 'Text',
            'attributes' => [
                'placeholder' => 'Email',
                'autofocus'   => 'autofocus'
            ]
        ]);

        $this->add([
            'name'       => 'credential',
            'type'       => 'Password',
            'attributes' => [
                'placeholder' => 'Password'
            ]
        ]);

        $this->add([
            'name' => 'token',
            'type' => 'Csrf'
        ]);

        $this->add([
            'name'    => 'submit',
            'type'    => 'Submit',
            'options' => [
                'label' => 'Submit'
            ]
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
                    ['name' => 'StringTrim'],
                ],
            ],
            [
                'name'     => 'credential',
                'required' => true
            ]
        ];
    }
}
