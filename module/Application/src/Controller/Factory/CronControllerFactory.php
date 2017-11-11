<?php

namespace Application\Controller\Factory;

use Application\Controller\CronController;
use Application\Service\AuthLogService;
use Application\Service\UserKeyService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CronControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services       = $controllers->getServiceLocator();
        $keysService    = $services->get(UserKeyService::class);
        $authLogService = $services->get(AuthLogService::class);

        return new CronController($authLogService, $keysService);
    }

}
