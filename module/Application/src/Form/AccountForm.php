<?php

namespace Application\Form;

use Application\Model\Folder;
use Zend\Filter\StringTrim;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Select;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\StringLength;

class AccountForm extends Form implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('form-folder');

        $this->add([
            'name'       => 'name',
            'type'       => Text::class,
            'options'    => [
                'label'            => 'Name',
                'column-size'      => 'sm-10',
                'label_attributes' => ['class' => 'col-sm-2'],
            ],
            'attributes' => [
                'autofocus' => 'autofocus',
                'maxlength' => 45,
            ],
        ]);

        $this->add([
            'name'    => 'folder_id',
            'type'    => Select::class,
            'options' => [
                'label'            => 'Folder',
                'column-size'      => 'sm-10',
                'label_attributes' => ['class' => 'col-sm-2'],
            ],
        ]);

        $this->add([
            'name'    => 'favorite',
            'type'    => Checkbox::class,
            'options' => [
                'label'       => 'Favorite',
                'column-size' => 'sm-10 col-sm-offset-2',
            ],
        ]);

        $this->add([
            'name' => 'data',
            'type' => AccountDataFieldset::class,
        ]);

        $this->add([
            'name' => 'token',
            'type' => Csrf::class,
        ]);

        $this->add([
            'name'       => 'submit',
            'type'       => Submit::class,
            'options'    => [
                'label'       => 'Save',
                'column-size' => 'sm-10 col-sm-offset-2',
            ],
            'attributes' => [
                'class' => 'btn btn-primary',
            ],
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
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 1,
                            'max' => 45,
                        ],
                    ],
                ],
            ]];
    }

    /**
     * Setup folders select
     * @param Folder[] $folders array of folders
     * @return self
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
     * @return self
     */
    public function setFolderId($id)
    {
        $this->get('folder_id')->setValue($id);

        return $this;
    }
}
