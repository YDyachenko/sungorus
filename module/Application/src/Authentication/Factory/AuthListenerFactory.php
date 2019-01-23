<?php

namespace Application\Authentication\Factory;

use Application\Authentication\AuthListener;
use Application\Service\AuthLogService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthListenerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authLogService = $container->get(AuthLogService::class);

        return new AuthListener($authLogService);
    }
}
