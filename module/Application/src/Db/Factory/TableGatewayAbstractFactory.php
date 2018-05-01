<?php

namespace Application\Db\Factory;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\Db\ResultSet\ResultSet;

class TableGatewayAbstractFactory implements AbstractFactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (substr($requestedName, -5) !== 'Table')
            return false;

        $config      = $container->get('config');
        $gatewayName = $this->getConfigKey($requestedName);

        if (!isset($config['tablegateways'][$gatewayName]))
            return false;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dbAdapter   = $container->get(Adapter::class);
        $config      = $container->get('config');
        $gatewayName = $this->getConfigKey($requestedName);
        $gwConfig    = $config['tablegateways'][$gatewayName];
        $entity      = $this->getEntityFromConfig($gwConfig, $requestedName);
        $resultSet   = new ResultSet(null, new $entity());

        return new TableGateway($gwConfig['table_name'], $dbAdapter, null, $resultSet);
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
        if (!isset($config['entity_class']) || !class_exists($config['entity_class'])) {
            throw new ServiceNotCreatedException(sprintf(
                'Unable to create instance for service "%s"; entity class cannot be found', $requestedName
            ));
        }
        return $config['entity_class'];
    }
    
    
    /**
     * @param string $name
     * @return string
     */
    protected function getConfigKey($name)
    {
        return strtolower(preg_replace('/(?<!^)([A-Z])/', '_$1', substr($name,0, -5)));
    }

}
