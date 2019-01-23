<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class FolderForm extends Form implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('form-folder');

        $this->add([
            'name'       => 'name',
            'type'       => 'Text',
            'options'    => [//                'label' => 'Name',
            ],
            'attributes' => [
                'autofocus'   => 'autofocus',
                'placeholder' => 'Name',
                'maxlength'   => 45
            ],
        ]);

        $this->add([
            'name' => 'token',
            'type' => 'Csrf'
        ]);

        $this->add([
            'name'    => 'submit',
            'type'    => 'Submit',
            'options' => [
                'label' => 'Save'
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
                'name'       => 'name',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => '45',
                        ],
                    ],
                ],
            ]];
    }
}
