<?php

namespace Application\Authentication\Factory;

use Interop\Container\ContainerInterface;
use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthenticationServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $callback = function ($hash, $password) {
            $bcrypt = new Bcrypt();
            return $bcrypt->verify($password, $hash);
        };

        $storage   = $container->get(StorageInterface::class);
        $dbAdapter = $container->get(DbAdapter::class);
        $adapter   = new CallbackCheckAdapter($dbAdapter, 'users', 'email', 'password', $callback);
        $service   = new AuthenticationService($storage, $adapter);

        return $service;
    }
}
