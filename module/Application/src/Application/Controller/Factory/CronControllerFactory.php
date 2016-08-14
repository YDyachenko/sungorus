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
        $config         = $services->get('config');
        $keysTable      = $services->get('EncryptionKeysTable');
        $authLogService = $services->get('AuthLogService');

        return new CronController($config, $authLogService, $keysTable);
    }

}
