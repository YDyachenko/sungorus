<?php

namespace Application\Listener\Factory;

use Application\Listener\AuthorizationListener;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthorizationListenerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AuthorizationListener(
            $container->get(AuthenticationServiceInterface::class)
        );
    }
}
