<?php

namespace Application\Service\Factory;

use Application\Service\AccountDataCipher;
use Psr\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountDataCipherFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        return AccountDataCipher::factory('mcrypt');
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, AccountDataCipher::class);
    }

}
