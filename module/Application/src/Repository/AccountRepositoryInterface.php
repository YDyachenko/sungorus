<?php

namespace Application\Repository;

use Application\Model\Account;
use Application\Model\Folder;
use Application\Model\User;

interface AccountRepositoryInterface
{

    /**
     * find account by id
     * @param int $id
     * @return Account
     * @throws AccountNotFoundException
     */
    public function findById($id);

    /**
     * Find accounts by user
     * @param User $user
     * @return Account[]
     */
    public function findByUser(User $user);

    /**
     * Find user favorite accounts
     * @param User $user
     * @return Account[]
     */
    public function findUserFavorites(User $user);

    /**
     * Find accounts by folder
     * @param Folder $folder
     * @return Account[]
     */
    public function findByFolder(Folder $folder);

    /**
     * Find accounts by name
     * @param string $name account name
     * @param User $user
     * @return Account[]
     */
    public function findByName($name, User $user);

    /**
     * Save account
     * @param Account $account
     * @return AccountRepositoryInterface
     */
    public function save(Account $account);

    /**
     * Delete account
     * @param Account $account
     * @return AccountRepositoryInterface
     */
    public function delete(Account $account);
}
