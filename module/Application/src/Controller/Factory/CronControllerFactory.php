<?php

namespace Application\Controller\Factory;

use Application\Controller\CronController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CronControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services       = $controllers->getServiceLocator();
        $keysService    = $services->get('UserKeyService');
        $authLogService = $services->get('AuthLogService');

        return new CronController($authLogService, $keysService);
    }

}
