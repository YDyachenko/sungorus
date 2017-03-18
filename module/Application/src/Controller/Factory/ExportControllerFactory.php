<?php

namespace Application\Controller\Factory;

use Application\Controller\ExportController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ExportControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services      = $controllers->getServiceLocator();
        $exportService = $services->get('ExportService');

        return new ExportController($exportService);
    }

}
