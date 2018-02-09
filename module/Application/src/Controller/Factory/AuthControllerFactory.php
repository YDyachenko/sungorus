<?php

namespace Application\Controller\Factory;

use Application\Controller\AuthController;
use Application\Repository\UserRepositoryInterface;
use Application\Service\UserKeyService;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services    = $controllers->getServiceLocator();
        $config      = $services->get('config');
        $authService = $services->get(AuthenticationServiceInterface::class);
        $keyService  = $services->get(UserKeyService::class);
        $users       = $services->get(UserRepositoryInterface::class);

        return new AuthController($config, $authService, $keyService, $users);
    }

}
