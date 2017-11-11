<?php

namespace Application\Model;

use Application\Model\UserEntity;
use Application\Model\AccountEntity;
use Application\Model\FolderEntity;
use Application\Exception\AccountNotFoundException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression as SqlExpression;

class AccountModel
{

    /**
     * Accounts table
     * @var TableGatewayInterface
     */
    protected $accountsTable;

    /**
     * AccountsData table
     * @var TableGateway 
     */
    protected $dataTable;

    /**
     * @param TableGatewayInterface $accountsTable Accounts table
     * @param TableGatewayInterface $dataTable AccountsData table
     */
    public function __construct(TableGatewayInterface $accountsTable, TableGatewayInterface $dataTable)
    {
        $this->accountsTable = $accountsTable;
        $this->dataTable     = $dataTable;
    }

    /**
     * Fetch account by id
     * @param int $id
     * @return AccountEntity
     * @throws AccountNotFoundException
     */
    public function fetchById($id)
    {
        $id     = (int) $id;
        $rowset = $this->accountsTable->select(['id' => $id]);
        $row    = $rowset->current();
        if (!$row) {
            throw new AccountNotFoundException('Could not find account #' . $id);
        }

        return $row;
    }

    /**
     * Fetch user accounts
     * @param UserEntity $user
     * @return AccountEntity[]
     */
    public function fetchByUser(UserEntity $user)
    {
        $rowset = $this->accountsTable->select(function (Select $select) use ($user) {
            $select->where(['user_id' => $user->getId()])
                   ->order(['favorite DESC', 'name ASC']);
        });
        return $rowset;
    }

    /**
     * Fetch user favorite accounts
     * @param UserEntity $user
     * @return AccountEntity[]
     */
    public function fetchUserFavorites(UserEntity $user)
    {
        $rowset = $this->accountsTable->select(function (Select $select) use ($user) {
            $where = [
                'user_id'  => $user->getId(),
                'favorite' => true
            ];
            $select->where($where)
                   ->order('name ASC');
        });
        return $rowset;
    }

    /**
     * Fetch accounts by folder
     * @param FolderEntity $folder
     * @return AccountEntity[]
     */
    public function fetchByFolder(FolderEntity $folder)
    {
        $rowset = $this->accountsTable->select(function (Select $select) use ($folder) {
            $where = [
                'folder_id' => $folder->getId(),
                'user_id'   => $folder->getUserId(),
            ];
            $select->where($where)
                   ->order(['favorite DESC', 'name ASC']);
        });
        return $rowset;
    }
    
    /**
     * Search accounts
     * @param string $name account name
     * @param UserEntity $user
     * @return AccountEntity[]
     */
    public function searchByName($name, UserEntity $user)
    {
        $rowset = $this->accountsTable->select(function (Select $select) use ($name, $user) {
            $select->where([
                'user_id' => $user->getId(),
                'name LIKE ?' => '%' . str_replace(['_', '%'], ['\_', '\%'], $name) . '%'
                ])
                ->order(['favorite DESC', 'name ASC']);
        });
        return $rowset;
    }

    /**
     * Fetch account data
     * @param AccountEntity $account
     * @return AccountDataEntity
     */
    public function fetchAccountData(AccountEntity $account)
    {
        $rowset = $this->dataTable->select(['account_id' => $account->getId()]);
        return $rowset->current();
    }

    /**
     * Save account into DB
     * @param AccountEntity $account
     * @return AccountModel
     */
    public function saveAccount(AccountEntity $account)
    {
        $data = $account->getArrayCopy();
        $id   = $data['id'];

        if ($id) {
            $data['date_modified'] = new SqlExpression('NOW()');
            $this->accountsTable->update($data, ['id' => $id]);
        } else {
            $data['date_created'] = new SqlExpression('NOW()');

            $id = $this->accountsTable->insert($data);
            $account->setId($this->accountsTable->getLastInsertValue());
        }

        return $this;
    }

    /**
     * Insert account data into DB
     * @param AccountDataEntity $data
     * @return AccountModel
     */
    public function insertAccountData(AccountDataEntity $data)
    {
        $hydrator = $this->dataTable->getResultSetPrototype()->getHydrator();
        $item     = $hydrator->extract($data);

        $this->dataTable->insert($item);

        return $this;
    }

    /**
     * Update account data
     * @param AccountDataEntity $data
     * @return AccountModel
     */
    public function updateAccountData(AccountDataEntity $data)
    {
        $hydrator = $this->dataTable->getResultSetPrototype()->getHydrator();
        $item     = $hydrator->extract($data);

        $this->dataTable->update($item, ['account_id' => $data->getAccountId()]);

        return $this;
    }

    /**
     * Delete account
     * @param AccountEntity $account
     * @return AccountModel
     */
    public function deleteAccount(AccountEntity $account)
    {
        $this->dataTable->delete(['account_id' => $account->getId()]);
        $this->accountsTable->delete(['id' => $account->getId()]);
        return $this;
    }

    /**
     * Set encryption key
     * @param string $key
     * @return AccountModel
     */
    public function setCryptKey($key)
    {
        $hydrator = $this->dataTable->getResultSetPrototype()->getHydrator();
        $hydrator->setCryptKey($key);

        return $this;
    }

    /**
     * Get accounts with data in folder
     * @param FolderEntity $folder
     * @return array
     */
    public function exportAccountsByFolder(FolderEntity $folder)
    {
        $accounts = $this->accountsTable->select([
                'folder_id' => $folder->getId(),
                'user_id'   => $folder->getUserId(),
            ])->buffer();
        
        $result = [
                'accounts' => $accounts,
                'data'     => []
            ];
        
        if (!$accounts->count())
            return $result;

        $ids  = $data = [];

        foreach ($accounts as $account) {
            $ids[] = $account->getId();
        }

        $dataRowSet = $this->dataTable->select(['account_id' => $ids]);

        foreach ($dataRowSet as $row) {
            $result['data'][$row->getAccountId()] = $row->getData();
        }

        return $result;
    }

}
