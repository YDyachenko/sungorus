<?php

namespace Application\Controller\Factory;

use Application\Controller\ExportController;
use Application\Service\ExportService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ExportControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services      = $controllers->getServiceLocator();
        $exportService = $services->get(ExportService::class);

        return new ExportController($exportService);
    }

}
