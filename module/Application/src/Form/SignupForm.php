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

        $this->add(array(
            'name'       => 'email',
            'type'       => 'Text',
            'attributes' => array(
                'placeholder' => 'Email',
                'autofocus'   => 'autofocus'
            )
        ));

        $this->add(array(
            'name'       => 'password',
            'type'       => 'Password',
            'attributes' => array(
                'placeholder' => 'Password'
            )
        ));

        $this->add(array(
            'name'       => 'confirm_pwd',
            'type'       => 'Password',
            'attributes' => array(
                'placeholder' => 'Confirm your password'
            )
        ));

        $this->add(array(
            'name'       => 'key',
            'type'       => 'Password',
            'attributes' => array(
                'placeholder' => 'Encryption key'
            )
        ));

        $this->add(array(
            'name'       => 'confirm_key',
            'type'       => 'Password',
            'attributes' => array(
                'placeholder' => 'Confirm your key'
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
                'name'       => 'email',
                'required'   => true,
                'filters'    => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => Validator\Db\NoRecordExists::class,
                        'options' => array(
                            'adapter'  => $this->dbAdapter,
                            'table'    => 'users',
                            'field'    => 'email',
                            'messages' => array(
                                Validator\Db\AbstractDb::ERROR_RECORD_FOUND => 'This email already used',
                            ),
                        ),
                    ),
                    array(
                        'name'    => Validator\EmailAddress::class,
                        'options' => array(
                            'useDomainCheck' => true,
                        ),
                    ),
                )
            ),
            array(
                'name'     => 'password',
                'required' => true
            ),
            array(
                'name'       => 'confirm_pwd',
                'required'   => true,
                'validators' => array(
                    array(
                        'name'    => Validator\Identical::class,
                        'options' => array(
                            'token'    => 'password',
                            'strict'   => true,
                            'messages' => array(
                                Validator\Identical::NOT_SAME => 'The two given passwords do not match',
                            ),
                        ),
                    ),
                )
            ),
            array(
                'name'     => 'key',
                'required' => true
            ),
            array(
                'name'       => 'confirm_key',
                'required'   => true,
                'validators' => array(
                    array(
                        'name'    => Validator\Identical::class,
                        'options' => array(
                            'token'    => 'key',
                            'strict'   => true,
                            'messages' => array(
                                \Zend\Validator\Identical::NOT_SAME => 'The two given keys do not match',
                            ),
                        ),
                    ),
                )
            ),
        );
    }

}
