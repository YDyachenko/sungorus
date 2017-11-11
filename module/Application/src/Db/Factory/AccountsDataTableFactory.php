<?php

namespace Application\Db\Factory;

use Application\Hydrator\AccountDataDecoder;
use Application\Model\AccountDataEntity;
use Psr\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Crypt\BlockCipher;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountsDataTableFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $dbAdapter   = $container->get(Adapter::class);
        $blockCipher = $container->get(BlockCipher::class);

        $hydrator           = new AccountDataDecoder($blockCipher);
        $resultSetPrototype = new HydratingResultSet($hydrator, new AccountDataEntity());

        return new TableGateway('accounts_data', $dbAdapter, null, $resultSetPrototype);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, 'AccountsDataTable');
    }

}
