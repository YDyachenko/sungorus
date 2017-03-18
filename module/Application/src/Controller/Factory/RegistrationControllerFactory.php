<?php

namespace Application\Controller\Factory;

use Application\Controller\RegistrationController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RegistrationControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services    = $controllers->getServiceLocator();
        $config      = $services->get('config');
        $form        = $services->get('SignupForm');
        $authService = $services->get('AuthService');
        $keyService  = $services->get('UserKeyService');
        $userModel   = $services->get('UserModel');

        return new RegistrationController($config, $form, $authService, $keyService, $userModel);
    }

}
