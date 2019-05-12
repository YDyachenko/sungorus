<?php

namespace Application\Form;

use Application\Validator\Bcrypt;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Password;
use Zend\Form\Element\Submit;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class EncryptionKeyForm extends Form implements InputFilterProviderInterface
{

    /**
     * @var string
     */
    protected $keyHash;

    public function __construct()
    {
        parent::__construct('form-encryption-key');

        $this->add([
            'name'       => 'key',
            'type'       => Password::class,
            'attributes' => [
                'autofocus' => 'autofocus',
            ],
        ]);

        $this->add([
            'name'       => 'remember',
            'type'       => Checkbox::class,
            'options'    => [
                'label' => 'Remember for 2 weeks',
            ],
            'attributes' => [
                'value' => '1',
            ],
        ]);

        $this->add([
            'name' => 'token',
            'type' => Csrf::class,
        ]);

        $this->add([
            'name'    => 'submit',
            'type'    => Submit::class,
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
                                Bcrypt::NOT_MATCH => 'The key is not valid',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
