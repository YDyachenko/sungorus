<?php

namespace Application\Authentication\Factory;

use Application\Repository\UserRepositoryInterface;
use Application\Authentication\Storage\SessionProxy;
use Psr\Container\ContainerInterface;
use Zend\Authentication\Storage\StorageInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StorageFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $users = $container->get(UserRepositoryInterface::class);

        return new SessionProxy($users);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, StorageInterface::class);
    }

}
