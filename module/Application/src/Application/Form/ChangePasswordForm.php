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

        $this->add(array(
            'name'       => 'old',
            'type'       => 'Password',
            'attributes' => array(
                'placeholder' => 'Old password',
                'autofocus'   => 'autofocus'
            )
        ));

        $this->add(array(
            'name'       => 'new',
            'type'       => 'Password',
            'attributes' => array(
                'placeholder' => 'New password'
            )
        ));

        $this->add(array(
            'name'       => 'confirm',
            'type'       => 'Password',
            'attributes' => array(
                'placeholder' => 'Retype new password'
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
        return array(
            array(
                'name'       => 'old',
                'required'   => true,
                'validators' => array(
                    array(
                        'name'    => '\Application\Validator\Bcrypt',
                        'options' => array(
                            'hash'     => $this->passwordHash,
                            'messages' => array(
                                \Application\Validator\Bcrypt::HASH => 'Wrong password',
                            ),
                        ),
                    ),
                )
            ),
            array(
                'name'     => 'new',
                'required' => true
            ),
            array(
                'name'       => 'confirm',
                'required'   => true,
                'validators' => array(
                    array(
                        'name'    => 'Identical',
                        'options' => array(
                            'token'    => 'new',
                            'strict'   => true,
                            'messages' => array(
                                \Zend\Validator\Identical::NOT_SAME => 'The two given passwords do not match',
                            ),
                        ),
                    ),
                )
            ),
        );
    }

}
