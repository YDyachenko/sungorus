<?php

namespace Application\Repository\Factory;

use Application\Repository\AccountRepository;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AccountRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $table = $container->get('AccountsTable');
        return new AccountRepository($table);
    }

}
