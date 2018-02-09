<?php

namespace Application\Repository\Factory;

use Application\Hydrator\AccountDataHydrator;
use Application\Repository\AccountDataRepository;
use Psr\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountDataRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $table = $container->get('AccountsDataTable');
        $hydrator = $container->get(AccountDataHydrator::class);
        return new AccountDataRepository($table, $hydrator);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, AccountDataRepository::class);
    }

}
