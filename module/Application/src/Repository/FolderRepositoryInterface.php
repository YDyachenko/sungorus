<?php

namespace Application\Repository;

use Application\Model\FolderEntity;
use Application\Model\UserEntity;

interface FolderRepositoryInterface
{

    /**
     * Find folder by id
     * @param int $id
     * @return FolderEntity
     * @throws FolderNotFoundException
     */
    public function findById($id);

    /**
     * Find folders by user
     * @param UserEntity $user
     * @return FolderEntity[]
     */
    public function findByUser(UserEntity $user);

    /**
     * Save folder
     * @param FolderEntity $folder
     * @return FolderRepository
     */
    public function save(FolderEntity $folder);

    /**
     * Delete folder
     * @param FolderEntity $folder
     * @return FolderRepository
     */
    public function delete(FolderEntity $folder);
}
