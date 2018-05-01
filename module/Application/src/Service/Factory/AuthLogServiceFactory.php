<?php

namespace Application\Service\Factory;

use Application\Service\AuthLogService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthLogServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $config       = $container->get('Config');
        $successTable = $container->get('AuthLogSuccessTable');
        $failureTable = $container->get('AuthLogFailureTable');

        return new AuthLogService($config, $successTable, $failureTable);
    }

}
