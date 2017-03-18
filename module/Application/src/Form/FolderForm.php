<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class FolderForm extends Form implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('form-folder');

        $this->add(array(
            'name'       => 'name',
            'type'       => 'Text',
            'options'    => array(
//                'label' => 'Name',
            ),
            'attributes' => array(
                'autofocus'   => 'autofocus',
                'placeholder' => 'Name',
                'maxlength'   => 45
            ),
        ));

        $this->add(array(
            'name' => 'token',
            'type' => 'Csrf'
        ));

        $this->add(array(
            'name'       => 'submit',
            'type'       => 'Submit',
            'options' => array(
                'label' => 'Save'
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
                'name'       => 'name',
                'required'   => true,
                'filters'    => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => '45',
                        ),
                    ),
                ),
        ));
    }

}
