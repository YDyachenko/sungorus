<?php

namespace Application\Authentication\Storage;

use Application\Model\UserEntity;
use Application\Repository\UserRepositoryInterface;
use Zend\Authentication\Storage\Session as SessionStorage;
use Zend\Authentication\Storage\StorageInterface;

class SessionProxy implements StorageInterface
{

    /**
     * @var SessionStorage 
     */
    protected $storage;

    /**
     * @var UserEntity
     */
    protected $resolvedIdentity;

    /**
     * @var UserRepositoryInterface 
     */
    protected $users;

    public function __construct(UserRepositoryInterface $users)
    {
        $this->users = $users;
    }

    /**
     * 
     * @return SessionStorage
     */
    protected function getStorage()
    {
        if (null === $this->storage) {
            $this->storage = new SessionStorage();
        }

        return $this->storage;
    }

    public function clear()
    {
        $this->getStorage()->clear();
    }

    public function isEmpty()
    {
        return $this->getStorage()->isEmpty();
    }

    public function read()
    {
        if (null === $this->resolvedIdentity) {
            $identity = $this->getStorage()->read();

            $this->resolvedIdentity = $this->users->FindByIdentity($identity);
        }

        return $this->resolvedIdentity;
    }

    public function write($contents)
    {
        $this->resolvedIdentity = null;
        $this->getStorage()->write($contents);
    }

}
