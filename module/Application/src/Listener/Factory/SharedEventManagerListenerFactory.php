<?php

namespace Application\Listener\Factory;

use Application\Authentication\AuthListener;
use Application\Listener\SharedEventManagerListener;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class SharedEventManagerListenerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SharedEventManagerListener(
            $container->get(AuthListener::class)
        );
    }
}
