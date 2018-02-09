<?php

namespace Application\Repository;

use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Select;
use Application\Model\UserEntity;
use Application\Model\FolderEntity;
use Application\Exception\FolderNotFoundException;

class FolderRepository implements FolderRepositoryInterface
{

    /**
     * Folders table
     * @var TableGatewayInterface
     */
    protected $table;

    /**
     * 
     * @param TableGatewayInterface $table
     */
    public function __construct(TableGatewayInterface $table)
    {
        $this->table = $table;
    }

    /**
     * {@inheritdoc}
     */
    public function findById($id)
    {
        $id     = (int) $id;
        $rowset = $this->table->select(['id' => $id]);
        $row    = $rowset->current();
        if (!$row) {
            throw new FolderNotFoundException('Could not find folder #' . $id);
        }

        return $row;
    }

    /**
     * {@inheritdoc}
     */
    public function findByUser(UserEntity $user)
    {
        $rowset = $this->table->select(function (Select $select) use ($user) {
            $select
                ->where(['user_id' => $user->getId()])
                ->order('name ASC');
        });

        return $rowset;
    }
    
    /**
     * {@inheritdoc}
     */
    public function save(FolderEntity $folder)
    {
        $data = $folder->getArrayCopy();
        
        $id = $data['id'];
        if ($id) {
            $this->table->update($data, ['id' => $id]);
        } else {
            $id = $this->table->insert($data);
            $folder->setId($this->table->getLastInsertValue());
        }
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function delete(FolderEntity $folder)
    {
        $this->table->delete(['id' => $folder->getId()]);
        return $this;
    }

}
