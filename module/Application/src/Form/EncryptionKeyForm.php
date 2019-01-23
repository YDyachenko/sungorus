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

        $this->add([
            'name'       => 'key',
            'type'       => 'Password',
            'attributes' => [
//                'placeholder' => 'Encryption key',
                'autofocus' => 'autofocus'
            ]
        ]);

        $this->add([
            'name'       => 'remember',
            'type'       => 'Checkbox',
            'options'    => [
                'label' => 'Remember for 2 weeks'
            ],
            'attributes' => [
                'value' => '1'
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
                'name'       => 'key',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => '\Application\Validator\Bcrypt',
                        'options' => [
                            'hash'     => $this->keyHash,
                            'messages' => [
                                Bcrypt::HASH => 'The key is not valid',
                            ],
                        ],
                    ],
                ]
            ]
        ];
    }
}
