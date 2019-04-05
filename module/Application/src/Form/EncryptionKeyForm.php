<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Application\Validator\Bcrypt;

class EncryptionKeyForm extends Form implements InputFilterProviderInterface
{

    /**
     * @var string
     */
    protected $keyHash;

    public function __construct()
    {
        parent::__construct('form-login');

        $this->add([
            'name'       => 'key',
            'type'       => 'Password',
            'attributes' => [
                'autofocus' => 'autofocus',
            ],
        ]);

        $this->add([
            'name'       => 'remember',
            'type'       => 'Checkbox',
            'options'    => [
                'label' => 'Remember for 2 weeks',
            ],
            'attributes' => [
                'value' => '1',
            ],
        ]);

        $this->add([
            'name' => 'token',
            'type' => 'Csrf',
        ]);

        $this->add([
            'name'    => 'submit',
            'type'    => 'Submit',
            'options' => [
                'label' => 'Submit',
            ],
        ]);
    }

    public function setKeyHash($hash)
    {
        $this->keyHash = $hash;
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
                        'name'    => Bcrypt::class,
                        'options' => [
                            'hash'     => $this->keyHash,
                            'messages' => [
                                Bcrypt::HASH => 'The key is not valid',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
