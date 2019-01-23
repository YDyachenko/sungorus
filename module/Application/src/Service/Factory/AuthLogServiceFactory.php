<?php

namespace Application\Service\Factory;

use Application\Db\TableGatewayPluginManager;
use Application\Service\AuthLogService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthLogServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $config       = $container->get('Config');
        $tables       = $container->get(TableGatewayPluginManager::class);
        $successTable = $tables->get('AuthLogSuccessTable');
        $failureTable = $tables->get('AuthLogFailureTable');

        return new AuthLogService($config, $successTable, $failureTable);
    }
}
