<?php

namespace Application\Repository\Factory;

use Application\Repository\UserRepository;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class UserRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $table = $container->get('UsersTable');
        return new UserRepository($table);
    }

}
