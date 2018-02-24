<?php

namespace Application\Repository;

use Application\Model\Folder;
use Application\Model\User;

interface FolderRepositoryInterface
{

    /**
     * Find folder by id
     * @param int $id
     * @return Folder
     * @throws FolderNotFoundException
     */
    public function findById($id);

    /**
     * Find folders by user
     * @param User $user
     * @return Folder[]
     */
    public function findByUser(User $user);

    /**
     * Save folder
     * @param Folder $folder
     * @return FolderRepository
     */
    public function save(Folder $folder);

    /**
     * Delete folder
     * @param Folder $folder
     * @return FolderRepository
     */
    public function delete(Folder $folder);
}
