<?php

namespace Application\Controller\Factory;

use Application\Controller\AuthController;
use Application\Model\UserModel;
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
        $userModel   = $services->get(UserModel::class);

        return new AuthController($config, $authService, $keyService, $userModel);
    }

}
