<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class LoginForm extends Form implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('form-login');

        $this->add(array(
            'name'       => 'identity',
            'type'       => 'Text',
            'attributes' => array(
                'placeholder' => 'Email',
                'autofocus'   => 'autofocus'
            )
        ));

        $this->add(array(
            'name'       => 'credential',
            'type'       => 'Password',
            'attributes' => array(
                'placeholder' => 'Password'
            )
        ));

        $this->add(array(
            'name' => 'token',
            'type' => 'Csrf'
        ));

        $this->add(array(
            'name'       => 'submit',
            'type'       => 'Submit',
            'options' => array(
                'label' => 'Submit'
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getInputFilterSpecification()
    {
        return array(
            array(
                'name'     => 'identity',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
            ),
            array(
                'name'     => 'credential',
                'required' => true
            )
        );
    }

}
