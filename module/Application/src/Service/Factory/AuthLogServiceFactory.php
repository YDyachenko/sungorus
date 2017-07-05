<?php

namespace Application\Service\Factory;

use Application\Service\AuthLogService;
use Psr\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthLogServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $config       = $container->get('Config');
        $successTable = $container->get('AuthLogSuccessTable');
        $failureTable = $container->get('AuthLogFailureTable');

        return new AuthLogService($config, $successTable, $failureTable);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, AuthLogService::class);
    }

}
