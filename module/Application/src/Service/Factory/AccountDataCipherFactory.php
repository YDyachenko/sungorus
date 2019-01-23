<?php

namespace Application\Service\Factory;

use Application\Service\AccountDataCipher;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AccountDataCipherFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return AccountDataCipher::factory('openssl');
    }
}
