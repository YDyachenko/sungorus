<?php

namespace Application\Controller\Plugin\Factory;

use Application\Controller\Plugin\SetEncryptionKeyCookie;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SetEncryptionKeyCookieFactory implements FactoryInterface
{

    /**
     * {@inheritDoc}
     *
     * @return SetEncryptionKeyCookie
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $config = $container->get('config');
        return new SetEncryptionKeyCookie($config);
    }

    /**
     * Create and return SetEncryptionKeyCookie instance
     *
     * For use with zend-servicemanager v2; proxies to __invoke().
     *
     * @param ServiceLocatorInterface $container
     * @return SetEncryptionKeyCookie
     */
    public function createService(ServiceLocatorInterface $container)
    {
        // Retrieve the parent container when under zend-servicemanager v2
        if (!method_exists($container, 'configure')) {
            $container = $container->getServiceLocator() ? : $container;
        }

        return $this($container, SetEncryptionKeyCookie::class);
    }

}
