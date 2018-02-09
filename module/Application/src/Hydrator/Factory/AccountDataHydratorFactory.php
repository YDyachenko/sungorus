<?php

namespace Application\Hydrator\Factory;

use Application\Hydrator\AccountDataHydrator;
use Psr\Container\ContainerInterface;
use Zend\Crypt\BlockCipher;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountDataHydratorFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $blockCipher = $container->get(BlockCipher::class);

        return new AccountDataHydrator($blockCipher);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, AccountDataHydrator::class);
    }

}
