<?php

namespace Application\Repository;

use Application\Model\AccountEntity;
use Application\Model\AccountDataEntity;

interface AccountDataRepositoryInterface
{

    /**
     * Find data by account
     * @param AccountEntity $account
     * @return AccountDataEntity
     */
    public function findByAccount(AccountEntity $account);

    /**
     * Save account data
     * @param AccountDataEntity $data
     * @return AccountDataRepositoryInterface
     */
    public function save(AccountDataEntity $data);
}
