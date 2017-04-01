<?php

namespace Application\Controller\Factory;

use Application\Controller\AuthController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services    = $controllers->getServiceLocator();
        $config      = $services->get('config');
        $authService = $services->get('AuthService');
        $keyService  = $services->get('UserKeyService');
        $userModel   = $services->get('UserModel');

        return new AuthController($config, $authService, $keyService, $userModel);
    }

}