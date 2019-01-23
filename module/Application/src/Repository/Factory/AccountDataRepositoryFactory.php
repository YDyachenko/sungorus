<?php

namespace Application\Repository\Factory;

use Application\Db\TableGatewayPluginManager;
use Application\Hydrator\AccountDataHydrator;
use Application\Repository\AccountDataRepository;
use Interop\Container\ContainerInterface;
use Zend\Hydrator\HydratorPluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class AccountDataRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $tables    = $container->get(TableGatewayPluginManager::class);
        $table     = $tables->get('AccountsDataTable');
        $hydrators = $container->get(HydratorPluginManager::class);
        $hydrator  = $hydrators->get(AccountDataHydrator::class);

        return new AccountDataRepository($table, $hydrator);
    }
}
