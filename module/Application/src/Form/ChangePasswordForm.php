<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class ChangePasswordForm extends Form implements InputFilterProviderInterface
{

    protected $passwordHash;

    public function __construct()
    {
        parent::__construct('form-change-password');

        $this->add([
            'name'       => 'old',
            'type'       => 'Password',
            'attributes' => [
                'placeholder' => 'Old password',
                'autofocus'   => 'autofocus'
            ]
        ]);

        $this->add([
            'name'       => 'new',
            'type'       => 'Password',
            'attributes' => [
                'placeholder' => 'New password'
            ]
        ]);

        $this->add([
            'name'       => 'confirm',
            'type'       => 'Password',
            'attributes' => [
                'placeholder' => 'Retype new password'
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
     * Set password hash. Hash used in \Application\Validator\Bcrypt
     * @param string $value Password hash
     * @return ChangePasswordForm
     */
    public function setPasswordHash($value)
    {
        $this->passwordHash = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInputFilterSpecification()
    {
        return [
            [
                'name'       => 'old',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => '\Application\Validator\Bcrypt',
                        'options' => [
                            'hash'     => $this->passwordHash,
                            'messages' => [
                                \Application\Validator\Bcrypt::HASH => 'Wrong password',
                            ],
                        ],
                    ],
                ]
            ],
            [
                'name'     => 'new',
                'required' => true
            ],
            [
                'name'       => 'confirm',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => 'Identical',
                        'options' => [
                            'token'    => 'new',
                            'strict'   => true,
                            'messages' => [
                                \Zend\Validator\Identical::NOT_SAME => 'The two given passwords do not match',
                            ],
                        ],
                    ],
                ]
            ],
        ];
    }
}
