<?php

namespace Application\Authentication\Factory;

use Application\Authentication\AuthFailureListener;
use Application\Service\AuthLogService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthFailureListenerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AuthFailureListener(
            $container->get(AuthLogService::class)
        );
    }
}
