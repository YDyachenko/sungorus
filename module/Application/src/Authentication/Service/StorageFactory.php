<?php

namespace Application\Authentication\Service;

use Application\Authentication\Storage\SessionProxy;
use Psr\Container\ContainerInterface;
use Zend\Authentication\Storage\StorageInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StorageFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $model = $container->get('UserModel');

        return new SessionProxy($model);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, StorageInterface::class);
    }

}
