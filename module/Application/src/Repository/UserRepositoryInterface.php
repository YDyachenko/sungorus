<?php

namespace Application\Repository;

use Application\Model\UserEntity;

interface UserRepositoryInterface
{

    /**
     * Find user by id
     * @param int $id
     * @return UserEntity
     * @throws UserNotFoundException
     */
    public function findById($id);

    /**
     * Find user by identity
     * @param string $identity
     * @return UserEntity
     * @throws UserNotFoundException
     */
    public function FindByIdentity($identity);

    /**
     * Create new user
     * @param array $data
     * @return UserEntity
     */
    public function createUser(array $data);

    /**
     * Save user
     * @param UserEntity $user
     * @return UserRepositoryInterface
     */
    public function save(UserEntity $user);
}
