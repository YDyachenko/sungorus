<?php

namespace Application\Listener\Factory;

use Application\Listener\SessionListener;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\ManagerInterface;

class SessionListenerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SessionListener(
            $container->get(ManagerInterface::class)
        );
    }
}
