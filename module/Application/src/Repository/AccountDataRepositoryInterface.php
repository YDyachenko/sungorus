<?php

namespace Application\Repository;

use Application\Model\Account;
use Application\Model\AccountData;

interface AccountDataRepositoryInterface
{

    /**
     * Find data by account
     * @param Account $account
     * @return AccountData
     */
    public function findByAccount(Account $account);

    /**
     * Save account data
     * @param AccountData $data
     * @return AccountDataRepositoryInterface
     */
    public function save(AccountData $data);
}
