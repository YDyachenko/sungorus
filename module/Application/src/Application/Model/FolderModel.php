<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Select;
use Application\Model\UserEntity;
use Application\Model\FolderEntity;
use Application\Exception\FolderNotFoundException;

class FolderModel
{

    /**
     * Folders table
     * @var TableGatewayInterface
     */
    protected $foldersTable;

    /**
     * @param TableGatewayInterface $foldersModel
     */
    public function __construct(TableGatewayInterface $foldersModel)
    {
        $this->foldersTable  = $foldersModel;
    }

    /**
     * Fetch folder by id
     * @param int $id
     * @return FolderEntity
     * @throws FolderNotFoundException
     */
    public function fetchById($id)
    {
        $id = (int)$id;
        $rowset = $this->foldersTable->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new FolderNotFoundException('Could not find folder #' . $id);
        }
        
        return $row;
    }
    
    /**
     * Fetch folders by user
     * @param UserEntity $user
     * @return FolderEntity[]
     */
    public function fetchByUser(UserEntity $user)
    {
        $rowset = $this->foldersTable->select(function (Select $select) use ($user) {
            $select->where(array('user_id' => $user->getId()))
                   ->order('name ASC');
        });
        
        return $rowset;
    }
    
    /**
     * Save folder into DB
     * @param FolderEntity $folder
     * @return FolderModel
     */
    public function saveFolder(FolderEntity $folder)
    {
        $data = $folder->getArrayCopy();
        
        $id = $data['id'];
        if ($id) {
            $this->foldersTable->update($data, array('id' => $id));
        } else {
            $id = $this->foldersTable->insert($data);
            $folder->setId($this->foldersTable->getLastInsertValue());
        }
        
        return $this;
    }
    
    /**
     * Delete folder
     * @param FolderEntity $folder
     * @return FolderModel
     */
    public function deleteFolder(FolderEntity $folder)
    {
        $this->foldersTable->delete(array('id' => $folder->getId()));
        return $this;
    }

}
