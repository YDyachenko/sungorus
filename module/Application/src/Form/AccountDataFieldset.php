<?php

namespace Application\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class AccountDataFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('data');

        $this->add(array(
            'name'       => 'login',
            'type'       => 'Text',
            'options'    => array(
                'label'            => 'Login',
                'column-size'      => 'sm-10',
                'label_attributes' => array('class' => 'col-sm-2')
            ),
            'attributes' => array(
//                'placeholder'  => 'Login',
                'autocomplete' => 'off',
            )
        ));

        

        $this->add(array(
            'name'       => 'password',
            'options'    => array(
                'label'            => 'Password',
                'column-size'      => 'sm-10',
                'label_attributes' => array('class' => 'col-sm-2')
            ),
            'type'       => 'Text',
            'attributes' => array(
//                'placeholder'  => 'Password',
                'autocomplete' => 'off',
            ),
        ));

        $this->add(array(
            'name'       => 'email',
            'type'       => 'Text',
            'options'    => array(
                'label'            => 'Email',
                'column-size'      => 'sm-10',
                'label_attributes' => array('class' => 'col-sm-2')
            ),
            'attributes' => array(
//                'placeholder'  => 'Email',
                'autocomplete' => 'off',
            )
        ));

        $this->add(array(
            'name'       => 'url',
            'type'       => 'url',
            'options'    => array(
                'label'            => 'URL',
                'column-size'      => 'sm-10',
                'label_attributes' => array('class' => 'col-sm-2')
            ),
            'attributes' => array(
//                'placeholder'  => 'URL',
                'autocomplete' => 'off',
            )
        ));

        $this->add(array(
            'name'    => 'notes',
            'type'    => 'Textarea',
            'options' => array(
                'label'            => 'Notes',
                'column-size'      => 'sm-10',
                'label_attributes' => array('class' => 'col-sm-2')
            ),
        ));
    }
    
    public function getInputFilterSpecification()
    {
        return array(
            array(
                'name'        => 'url',
                'required'    => true,
                'allow_empty' => true,
                'filters'     => array(
                    array('name' => 'StringTrim'),
                ),
                'validators'  => array(
                    array(
                        'name'    => 'Uri',
                        'options' => array(
                            'allowAbsolute' => true,
                            'allowRelative' => false,
                        ),
                    ),
                ),
        ));
    }

}
