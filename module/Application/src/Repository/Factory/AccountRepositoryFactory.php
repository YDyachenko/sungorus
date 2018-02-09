<?php

namespace Application\Repository\Factory;

use Application\Repository\AccountRepository;
use Psr\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $table = $container->get('AccountsTable');
        return new AccountRepository($table);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, AccountRepository::class);
    }

}
