<?php

namespace Application\Hydrator\Factory;

use Application\Hydrator\AccountDataHydrator;
use Application\Service\AccountDataCipher;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AccountDataHydratorFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $cipher = $container->get(AccountDataCipher::class);

        return new AccountDataHydrator($cipher);
    }
}
