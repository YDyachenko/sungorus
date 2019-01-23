<?php

namespace Application\Db\Factory;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Hydrator\HydratorPluginManager;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class TableGatewayAbstractFactory implements AbstractFactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $config = $container->get('config');

        if (! isset($config['tablegateways'][$requestedName])) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dbAdapter          = $container->get(Adapter::class);
        $config             = $container->get('config');
        $gwConfig           = $config['tablegateways'][$requestedName];
        $entity             = $this->getEntityFromConfig($gwConfig, $requestedName);
        $hydrator           = $this->getHydratorFromConfig($gwConfig, $container);
        $resultSetPrototype = new HydratingResultSet($hydrator, new $entity());

        return new TableGateway($gwConfig['table_name'], $dbAdapter, null, $resultSetPrototype);
    }

    /**
     * Get entity class
     *
     * @param array $config
     * @param string $requestedName
     * @return string
     * @throws ServiceNotCreatedException
     */
    protected function getEntityFromConfig(array $config, $requestedName)
    {
        if (! isset($config['entity_class']) || ! class_exists($config['entity_class'])) {
            throw new ServiceNotCreatedException(sprintf(
                'Unable to create instance for service "%s"; entity class cannot be found',
                $requestedName
            ));
        }
        return $config['entity_class'];
    }

    /**
     * Retrieve the configured hydrator.
     *
     * If configuration defines a `hydrator_name`, that service will be
     * retrieved from the HydratorManager; otherwise ArraySerializable
     * will be retrieved.
     *
     * @param array $config
     * @param ContainerInterface $container
     * @return \Zend\Hydrator\HydratorInterface
     */
    protected function getHydratorFromConfig(array $config, ContainerInterface $container)
    {
        $hydratorName = isset($config['hydrator_name']) ? $config['hydrator_name'] : ArraySerializable::class;
        $hydrators    = $container->get(HydratorPluginManager::class);
        return $hydrators->get($hydratorName);
    }
}
