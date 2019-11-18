<?php

namespace Application\Authentication\Factory;

use Application\Authentication\AuthSuccessListener;
use Application\Service\AuthLogService;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthSuccessListenerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AuthSuccessListener(
            $container->get(AuthLogService::class),
            $container->get(AuthenticationServiceInterface::class)
        );
    }
}
