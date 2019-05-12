<?php

namespace Application\Form;

use Application\Validator\Bcrypt;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Password;
use Zend\Form\Element\Submit;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\Identical;

class ChangePasswordForm extends Form implements InputFilterProviderInterface
{

    protected $passwordHash;

    public function __construct()
    {
        parent::__construct('form-change-password');

        $this->add([
            'name'       => 'current',
            'type'       => Password::class,
            'options'    => [
                'label' => 'Current',
            ],
            'attributes' => [
                'autofocus' => 'autofocus',
            ],
        ]);

        $this->add([
            'name'    => 'new',
            'type'    => Password::class,
            'options' => [
                'label' => 'New',
            ],
        ]);

        $this->add([
            'name'    => 'confirm',
            'type'    => Password::class,
            'options' => [
                'label' => 'Confirm',
            ],
        ]);

        $this->add([
            'name' => 'token',
            'type' => Csrf::class,
        ]);

        $this->add([
            'name'       => 'submit',
            'type'       => Submit::class,
            'options'    => [
                'label' => 'Save',
            ],
            'attributes' => [
                'class' => 'btn btn-primary',
            ],
        ]);
    }

    /**
     * Set password hash. Hash used in \Application\Validator\Bcrypt
     * @param string $value Password hash
     * @return self
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
                'name'       => 'current',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => Bcrypt::class,
                        'options' => [
                            'hash'     => $this->passwordHash,
                            'messages' => [
                                Bcrypt::NOT_MATCH => 'Wrong password',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name'     => 'new',
                'required' => true,
            ],
            [
                'name'       => 'confirm',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => Identical::class,
                        'options' => [
                            'token'    => 'new',
                            'strict'   => true,
                            'messages' => [
                                Identical::NOT_SAME => 'The two given passwords do not match',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
