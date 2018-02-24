<?php

namespace Application\Repository;

use Application\Model\User;

interface UserRepositoryInterface
{

    /**
     * Find user by id
     * @param int $id
     * @return User
     * @throws UserNotFoundException
     */
    public function findById($id);

    /**
     * Find user by identity
     * @param string $identity
     * @return User
     * @throws UserNotFoundException
     */
    public function FindByIdentity($identity);

    /**
     * Create new user
     * @param array $data
     * @return User
     */
    public function createUser(array $data);

    /**
     * Save user
     * @param User $user
     * @return UserRepositoryInterface
     */
    public function save(User $user);
}
