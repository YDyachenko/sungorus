<?php

namespace Application\Service\Factory;

use Application\Service\ExportService;
use Psr\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ExportServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $folderModel  = $container->get('FolderModel');
        $accountModel = $container->get('AccountModel');

        return new ExportService($folderModel, $accountModel);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, ExportService::class);
    }

}
