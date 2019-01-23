<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator;

class SignupForm extends Form implements InputFilterProviderInterface
{
    protected $dbAdapter;

    /**
     * @param AdapterInterface $dbAdapter Database adapter
     */
    public function __construct(AdapterInterface $dbAdapter)
    {
        parent::__construct('form-signup');

        $this->dbAdapter = $dbAdapter;

        $this->add([
            'name'       => 'email',
            'type'       => 'Text',
            'attributes' => [
                'placeholder' => 'Email',
                'autofocus'   => 'autofocus'
            ]
        ]);

        $this->add([
            'name'       => 'password',
            'type'       => 'Password',
            'attributes' => [
                'placeholder' => 'Password'
            ]
        ]);

        $this->add([
            'name'       => 'confirm_pwd',
            'type'       => 'Password',
            'attributes' => [
                'placeholder' => 'Confirm your password'
            ]
        ]);

        $this->add([
            'name'       => 'key',
            'type'       => 'Password',
            'attributes' => [
                'placeholder' => 'Encryption key'
            ]
        ]);

        $this->add([
            'name'       => 'confirm_key',
            'type'       => 'Password',
            'attributes' => [
                'placeholder' => 'Confirm your key'
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
                'name'       => 'email',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => Validator\Db\NoRecordExists::class,
                        'options' => [
                            'adapter'  => $this->dbAdapter,
                            'table'    => 'users',
                            'field'    => 'email',
                            'messages' => [
                                Validator\Db\AbstractDb::ERROR_RECORD_FOUND => 'This email already used',
                            ],
                        ],
                    ],
                    [
                        'name'    => Validator\EmailAddress::class,
                        'options' => [
                            'useDomainCheck' => true,
                        ],
                    ],
                ]
            ],
            [
                'name'     => 'password',
                'required' => true
            ],
            [
                'name'       => 'confirm_pwd',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => Validator\Identical::class,
                        'options' => [
                            'token'    => 'password',
                            'strict'   => true,
                            'messages' => [
                                Validator\Identical::NOT_SAME => 'The two given passwords do not match',
                            ],
                        ],
                    ],
                ]
            ],
            [
                'name'     => 'key',
                'required' => true
            ],
            [
                'name'       => 'confirm_key',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => Validator\Identical::class,
                        'options' => [
                            'token'    => 'key',
                            'strict'   => true,
                            'messages' => [
                                \Zend\Validator\Identical::NOT_SAME => 'The two given keys do not match',
                            ],
                        ],
                    ],
                ]
            ],
        ];
    }
}
