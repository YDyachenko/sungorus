<?php

namespace Application\Repository;

use Application\Model\UserEntity;
use Application\Model\AccountEntity;
use Application\Model\FolderEntity;
use Application\Exception\AccountNotFoundException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression as SqlExpression;

class AccountRepository implements AccountRepositoryInterface
{

    /**
     * Accounts table
     * @var TableGatewayInterface
     */
    protected $table;

    /**
     * @param TableGatewayInterface $accountsTable Accounts table
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
            throw new AccountNotFoundException('Could not find account #' . $id);
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
                ->order(['favorite DESC', 'name ASC']);
        });
        return $rowset;
    }

    /**
     * {@inheritdoc}
     */
    public function findUserFavorites(UserEntity $user)
    {
        $rowset = $this->table->select(function (Select $select) use ($user) {
            $select
                ->where([
                    'user_id'  => $user->getId(),
                    'favorite' => true
                ])
                ->order('name ASC');
        });
        return $rowset;
    }

    /**
     * {@inheritdoc}
     */
    public function findByFolder(FolderEntity $folder)
    {
        $rowset = $this->table->select(function (Select $select) use ($folder) {
            $select
                ->where([
                    'folder_id' => $folder->getId(),
                    'user_id'   => $folder->getUserId(),
                ])
                ->order(['favorite DESC', 'name ASC']);
        });
        return $rowset;
    }

    /**
     * {@inheritdoc}
     */
    public function findByName($name, UserEntity $user)
    {
        $rowset = $this->table->select(function (Select $select) use ($name, $user) {
            $param = '%' . str_replace(['_', '%'], ['\_', '\%'], $name) . '%';
            $select
                ->where([
                    'user_id'     => $user->getId(),
                    'name LIKE ?' => $param
                ])
                ->order(['favorite DESC', 'name ASC']);
        });
        return $rowset;
    }

    /**
     * {@inheritdoc}
     */
    public function save(AccountEntity $account)
    {
        $data = $account->getArrayCopy();
        $id   = $data['id'];

        if ($id) {
            $data['date_modified'] = new SqlExpression('NOW()');
            $this->table->update($data, ['id' => $id]);
        } else {
            $data['date_created'] = new SqlExpression('NOW()');

            $id = $this->table->insert($data);
            $account->setId($this->table->getLastInsertValue());
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AccountEntity $account)
    {
        $this->table->delete(['id' => $account->getId()]);
        return $this;
    }

}
