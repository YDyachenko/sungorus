<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class AccountForm extends Form implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('form-folder');

        $this->add([
            'name'       => 'name',
            'type'       => 'Text',
            'options'    => [
                'label'            => 'Name',
                'column-size'      => 'sm-10',
                'label_attributes' => ['class' => 'col-sm-2']
            ],
            'attributes' => [
                'autofocus' => 'autofocus',
//                'placeholder' => 'Name',
                'maxlength' => 45
            ],
        ]);

        $this->add([
            'name'    => 'folder_id',
            'type'    => 'Select',
            'options' => [
                'label'            => 'Folder',
                'column-size'      => 'sm-10',
                'label_attributes' => ['class' => 'col-sm-2']
            ]
        ]);

        $this->add([
            'name'    => 'favorite',
            'type'    => 'Checkbox',
            'options' => [
                'label'       => 'Favorite',
                'column-size' => 'sm-10 col-sm-offset-2'
            ],
        ]);

        $this->add([
            'name' => 'data',
            'type' => '\Application\Form\AccountDataFieldset',
        ]);

        $this->add([
            'name' => 'token',
            'type' => 'Csrf'
        ]);

        $this->add([
            'name'    => 'submit',
            'type'    => 'Submit',
            'options' => [
                'label'       => 'Save',
                'column-size' => 'sm-10 col-sm-offset-2'
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

    /**
     * Setup folders select
     * @param FolderEntity[] $folders array of folders
     * @return AccountForm
     */
    public function setFoldersOptions($folders)
    {
        $options = [];

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
