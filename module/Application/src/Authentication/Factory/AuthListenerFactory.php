<?php

namespace Application\Authentication\Factory;

use Application\Authentication\AuthListener;
use Psr\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthListenerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $authLogService = $container->get('AuthLogService');

        return new AuthListener($authLogService);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, AuthListener::class);
    }

}
