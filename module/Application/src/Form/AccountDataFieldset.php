<?php

namespace Application\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class AccountDataFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('data');

        $this->add([
            'name'       => 'login',
            'type'       => 'Text',
            'options'    => [
                'label'            => 'Login',
                'column-size'      => 'sm-10',
                'label_attributes' => ['class' => 'col-sm-2']
            ],
            'attributes' => [
//                'placeholder'  => 'Login',
                'autocomplete' => 'off',
            ]
        ]);

        $this->add([
            'name'       => 'email',
            'type'       => 'Text',
            'options'    => [
                'label'            => 'Email',
                'column-size'      => 'sm-10',
                'label_attributes' => ['class' => 'col-sm-2']
            ],
            'attributes' => [
//                'placeholder'  => 'Email',
                'autocomplete' => 'off',
            ]
        ]);

        $this->add([
            'name'       => 'password',
            'options'    => [
                'label'            => 'Password',
                'column-size'      => 'sm-10',
                'label_attributes' => ['class' => 'col-sm-2']
            ],
            'type'       => 'Text',
            'attributes' => [
//                'placeholder'  => 'Password',
                'autocomplete' => 'off',
            ],
        ]);

        $this->add([
            'name'       => 'url',
            'type'       => 'url',
            'options'    => [
                'label'            => 'URL',
                'column-size'      => 'sm-10',
                'label_attributes' => ['class' => 'col-sm-2']
            ],
            'attributes' => [
//                'placeholder'  => 'URL',
                'autocomplete' => 'off',
            ]
        ]);

        $this->add([
            'name'    => 'notes',
            'type'    => 'Textarea',
            'options' => [
                'label'            => 'Notes',
                'column-size'      => 'sm-10',
                'label_attributes' => ['class' => 'col-sm-2']
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            [
                'name'        => 'url',
                'required'    => true,
                'allow_empty' => true,
                'filters'     => [
                    ['name' => 'StringTrim'],
                ],
                'validators'  => [
                    [
                        'name'    => 'Uri',
                        'options' => [
                            'allowAbsolute' => true,
                            'allowRelative' => false,
                        ],
                    ],
                ],
            ]];
    }
}
