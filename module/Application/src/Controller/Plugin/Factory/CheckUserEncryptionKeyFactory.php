<?php

namespace Application\Controller\Plugin\Factory;

use Application\Controller\Plugin\CheckUserEncryptionKey;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CheckUserEncryptionKeyFactory implements FactoryInterface
{

    /**
     * {@inheritDoc}
     *
     * @return CheckUserEncryptionKey
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $keyService   = $container->get('UserKeyService');
        $accountModel = $container->get('AccountModel');
        $config       = $container->get('config');
        return new CheckUserEncryptionKey($keyService, $accountModel, $config);
    }

    /**
     * Create and return CheckUserEncryptionKey instance
     *
     * For use with zend-servicemanager v2; proxies to __invoke().
     *
     * @param ServiceLocatorInterface $container
     * @return CheckUserEncryptionKey
     */
    public function createService(ServiceLocatorInterface $container)
    {
        // Retrieve the parent container when under zend-servicemanager v2
        if (!method_exists($container, 'configure')) {
            $container = $container->getServiceLocator() ? : $container;
        }

        return $this($container, CheckUserEncryptionKey::class);
    }

}
