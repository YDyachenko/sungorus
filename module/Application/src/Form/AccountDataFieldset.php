<?php

namespace Application\Form;

use Zend\Filter\StringTrim;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Element\Url;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\Uri;

class AccountDataFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('data');

        $this->add([
            'name'       => 'login',
            'type'       => Text::class,
            'options'    => [
                'label'            => 'Login',
                'column-size'      => 'sm-10',
                'label_attributes' => ['class' => 'col-sm-2'],
            ],
            'attributes' => [
                'autocomplete' => 'off',
            ],
        ]);

        $this->add([
            'name'       => 'email',
            'type'       => Text::class,
            'options'    => [
                'label'            => 'Email',
                'column-size'      => 'sm-10',
                'label_attributes' => ['class' => 'col-sm-2'],
            ],
            'attributes' => [
                'autocomplete' => 'off',
            ],
        ]);

        $this->add([
            'name'       => 'password',
            'options'    => [
                'label'            => 'Password',
                'column-size'      => 'sm-10',
                'label_attributes' => ['class' => 'col-sm-2'],
            ],
            'type'       => Text::class,
            'attributes' => [
                'autocomplete' => 'off',
            ],
        ]);

        $this->add([
            'name'       => 'url',
            'type'       => Url::class,
            'options'    => [
                'label'            => 'URL',
                'column-size'      => 'sm-10',
                'label_attributes' => ['class' => 'col-sm-2'],
            ],
            'attributes' => [
                'autocomplete' => 'off',
            ],
        ]);

        $this->add([
            'name'    => 'notes',
            'type'    => Textarea::class,
            'options' => [
                'label'            => 'Notes',
                'column-size'      => 'sm-10',
                'label_attributes' => ['class' => 'col-sm-2'],
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getInputFilterSpecification()
    {
        return [
            [
                'name'        => 'url',
                'required'    => true,
                'allow_empty' => true,
                'filters'     => [
                    ['name' => StringTrim::class],
                ],
                'validators'  => [
                    [
                        'name'    => Uri::class,
                        'options' => [
                            'allowAbsolute' => true,
                            'allowRelative' => false,
                        ],
                    ],
                ],
            ]];
    }
}
