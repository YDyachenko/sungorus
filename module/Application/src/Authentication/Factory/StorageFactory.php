<?php

namespace Application\Authentication\Factory;

use Application\Authentication\Storage\SessionProxy;
use Application\Model\UserModel;
use Psr\Container\ContainerInterface;
use Zend\Authentication\Storage\StorageInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StorageFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $model = $container->get(UserModel::class);

        return new SessionProxy($model);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, StorageInterface::class);
    }

}
