<?php

namespace Application\Hydrator\Factory;

use Application\Hydrator\AccountDataHydrator;
use Application\Service\AccountDataCipher;
use Psr\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountDataHydratorFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $cipher = $container->get(AccountDataCipher::class);

        return new AccountDataHydrator($cipher);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, AccountDataHydrator::class);
    }

}