<?php

namespace Application\Db\Factory;

use Application\Hydrator\AccountDataHydrator;
use Application\Model\AccountData;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\HydratorPluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class AccountsDataTableFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dbAdapter = $container->get(Adapter::class);
        $hydrators = $container->get(HydratorPluginManager::class);
        $hydrator  = $hydrators->get(AccountDataHydrator::class);

        $resultSetPrototype = new HydratingResultSet($hydrator, new AccountData());

        return new TableGateway('accounts_data', $dbAdapter, null, $resultSetPrototype);
    }

}
