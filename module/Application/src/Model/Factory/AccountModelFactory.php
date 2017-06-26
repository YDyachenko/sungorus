<?php

namespace Application\Model\Factory;

use Application\Model\AccountModel;
use Psr\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountModelFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $accountsTable = $container->get('AccountsTable');
        $dataTable     = $container->get('AccountsDataTable');
        return new AccountModel($accountsTable, $dataTable);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, AccountModel::class);
    }

}
