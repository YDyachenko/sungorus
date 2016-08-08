<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class AccountForm extends Form implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('form-folder');

        $this->add(array(
            'name'       => 'name',
            'type'       => 'Text',
            'options'    => array(
                'label'            => 'Name',
                'column-size'      => 'sm-10',
                'label_attributes' => array('class' => 'col-sm-2')
            ),
            'attributes' => array(
                'autofocus' => 'autofocus',
//                'placeholder' => 'Name',
                'maxlength' => 45
            ),
        ));

        $this->add(array(
            'name'    => 'folder_id',
            'type'    => 'Select',
            'options' => array(
                'label'            => 'Folder',
                'column-size'      => 'sm-10',
                'label_attributes' => array('class' => 'col-sm-2')
            )
        ));

        $this->add(array(
            'name'    => 'favorite',
            'type'    => 'Checkbox',
            'options' => array(
                'label'       => 'Favorite',
                'column-size' => 'sm-10 col-sm-offset-2'
            ),
        ));

        $this->add(array(
            'name' => 'data',
            'type' => '\Application\Form\AccountDataFieldset',
        ));

        $this->add(array(
            'name' => 'token',
            'type' => 'Csrf'
        ));

        $this->add(array(
            'name'    => 'submit',
            'type'    => 'Submit',
            'options' => array(
                'label'       => 'Save',
                'column-size' => 'sm-10 col-sm-offset-2'
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

    /**
     * Setup folders select
     * @param FolderEntity[] $folders array of folders
     * @return AccountForm
     */
    public function setFoldersOptions($folders)
    {
        $options = array();

        foreach ($folders as $folder) {
            $options[$folder->getId()] = $folder->getName();
        }

        $this->get('folder_id')->setValueOptions($options);

        return $this;
    }

    /**
     * Set selected folder
     * @param int $id Folder id
     */
    public function setFolderId($id)
    {
        $this->get('folder_id')->setValue($id);
    }

}
