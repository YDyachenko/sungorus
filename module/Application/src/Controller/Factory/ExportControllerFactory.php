<?php

namespace Application\Controller\Factory;

use Application\Controller\ExportController;
use Application\Service\ExportService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ExportControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $services      = $container->getServiceLocator();
        $exportService = $services->get(ExportService::class);

        return new ExportController($exportService);
    }

}
