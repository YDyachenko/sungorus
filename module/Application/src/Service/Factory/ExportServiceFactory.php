<?php

namespace Application\Service\Factory;

use Application\Service\AccountDataCipher;
use Application\Service\ExportService;
use Psr\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ExportServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $adapter = $container->get(Adapter::class);
        $cipher  = $container->get(AccountDataCipher::class);

        return new ExportService($adapter, $cipher);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, ExportService::class);
    }

}
