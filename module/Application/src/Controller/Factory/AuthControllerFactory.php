<?php

namespace Application\Controller\Factory;

use Application\Controller\AuthController;
use Application\Repository\UserRepositoryInterface;
use Application\Service\UserKeyService;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config      = $container->get('config');
        $authService = $container->get(AuthenticationServiceInterface::class);
        $keyService  = $container->get(UserKeyService::class);
        $users       = $container->get(UserRepositoryInterface::class);

        return new AuthController($config, $authService, $keyService, $users);
    }
}
