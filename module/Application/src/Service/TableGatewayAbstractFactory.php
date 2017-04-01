<?php

namespace Application\Service;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\ResultSet\ResultSet;

class TableGatewayAbstractFactory implements AbstractFactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (substr($requestedName, -5) !== 'Table')
            return false;

        $config      = $serviceLocator->get('config');
        $gatewayName = $this->getConfigKey($requestedName);

        if (!isset($config['tablegateways'][$gatewayName]))
            return false;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $dbAdapter   = $serviceLocator->get(Adapter::class);
        $config      = $serviceLocator->get('config');
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
