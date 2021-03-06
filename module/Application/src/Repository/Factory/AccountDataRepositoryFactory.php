<?php

namespace Application\Repository\Factory;

use Application\Hydrator\AccountDataHydrator;
use Application\Repository\AccountDataRepository;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AccountDataRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $table = $container->get('AccountsDataTable');
        $hydrator = $container->get(AccountDataHydrator::class);
        return new AccountDataRepository($table, $hydrator);
    }

}
