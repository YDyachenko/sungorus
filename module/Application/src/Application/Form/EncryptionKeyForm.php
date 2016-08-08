<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Application\Validator\Bcrypt;

class EncryptionKeyForm extends Form implements InputFilterProviderInterface
{

    protected $keyHash;

    /**
     * @param string $keyHash hash of encryption key.
     */
    public function __construct($keyHash)
    {
        parent::__construct('form-login');

        $this->keyHash = $keyHash;

        $this->add(array(
            'name'       => 'key',
            'type'       => 'Password',
            'attributes' => array(
//                'placeholder' => 'Encryption key',
                'autofocus' => 'autofocus'
            )
        ));
        
        $this->add(array(
            'name'       => 'remember',
            'type'       => 'Checkbox',
            'options' => array(
                'label' => 'Remember for 2 weeks'
            ),
            'attributes' => array(
                'value' => '1'
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
                'name'       => 'key',
                'required'   => true,
                'validators' => array(
                    array(
                        'name'    => '\Application\Validator\Bcrypt',
                        'options' => array(
                            'hash'     => $this->keyHash,
                            'messages' => array(
                                Bcrypt::HASH => 'The key is not valid',
                            ),
                        ),
                    ),
                )
            )
        );
    }

}
