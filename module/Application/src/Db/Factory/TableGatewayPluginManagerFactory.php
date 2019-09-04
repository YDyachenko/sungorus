<?php

namespace Application\Db\Factory;

use Application\Db\TableGatewayPluginManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class TableGatewayPluginManagerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return TableGatewayPluginManager
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        return new TableGatewayPluginManager($container, [
            'abstract_factories' => [
                TableGatewayAbstractFactory::class,
            ],
        ]);
    }
}
