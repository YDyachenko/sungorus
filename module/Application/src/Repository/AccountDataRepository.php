<?php

namespace Application\Repository;

use Application\Model\Account;
use Application\Model\AccountData;
use Zend\Hydrator\HydratorInterface;
use Zend\Db\TableGateway\TableGatewayInterface;

class AccountDataRepository implements AccountDataRepositoryInterface
{

    /**
     * Data table
     * @var TableGatewayInterface
     */
    protected $table;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    public function __construct(TableGatewayInterface $table, HydratorInterface $hydrator)
    {
        $this->table    = $table;
        $this->hydrator = $hydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function findByAccount(Account $account)
    {
        $rowset = $this->table->select(['account_id' => $account->getId()]);
        $row    = $rowset->current();

        return $row;
    }

    /**
     * {@inheritdoc}
     */
    public function save(AccountData $data)
    {
        $this->table->delete(['account_id' => $data->getAccountId()]);
        $values = $this->hydrator->extract($data);
        $this->table->insert($values);

        return $this;
    }

}
