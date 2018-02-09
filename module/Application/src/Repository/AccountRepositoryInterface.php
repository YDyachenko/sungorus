<?php

namespace Application\Repository;

use Application\Model\AccountEntity;
use Application\Model\FolderEntity;
use Application\Model\UserEntity;

interface AccountRepositoryInterface
{

    /**
     * find account by id
     * @param int $id
     * @return AccountEntity
     * @throws AccountNotFoundException
     */
    public function findById($id);

    /**
     * Find accounts by user
     * @param UserEntity $user
     * @return AccountEntity[]
     */
    public function findByUser(UserEntity $user);

    /**
     * Find user favorite accounts
     * @param UserEntity $user
     * @return AccountEntity[]
     */
    public function findUserFavorites(UserEntity $user);

    /**
     * Find accounts by folder
     * @param FolderEntity $folder
     * @return AccountEntity[]
     */
    public function findByFolder(FolderEntity $folder);

    /**
     * Find accounts by name
     * @param string $name account name
     * @param UserEntity $user
     * @return AccountEntity[]
     */
    public function findByName($name, UserEntity $user);

    /**
     * Save account
     * @param AccountEntity $account
     * @return AccountRepositoryInterface
     */
    public function save(AccountEntity $account);

    /**
     * Delete account
     * @param AccountEntity $account
     * @return AccountRepositoryInterface
     */
    public function delete(AccountEntity $account);
}
