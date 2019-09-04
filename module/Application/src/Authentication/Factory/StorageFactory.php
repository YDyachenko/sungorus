<?php

namespace Application\Authentication\Factory;

use Application\Authentication\Storage\SessionProxy;
use Application\Repository\UserRepositoryInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class StorageFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $users = $container->get(UserRepositoryInterface::class);

        return new SessionProxy($users);
    }
}
