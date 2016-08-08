<?php

namespace Application\Authentication\Storage;

use Application\Model\UserEntity;
use Application\Model\UserModel;
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
     * @var UserModel 
     */
    protected $model;

    public function __construct(UserModel $model)
    {
        $this->model = $model;
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

            $this->resolvedIdentity = $this->model->fetchByIdentity($identity);
        }

        return $this->resolvedIdentity;
    }

    public function write($contents)
    {
        $this->resolvedIdentity = null;
        $this->getStorage()->write($contents);
    }

}
