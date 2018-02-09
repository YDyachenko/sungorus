<?php

namespace Application\Listener\Factory;

use Application\Listener\EncryptionKeyListener;
use Application\Hydrator\AccountDataHydrator;
use Application\Service\UserKeyService;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EncryptionKeyListenerFactory implements FactoryInterface
{

    /**
     * {@inheritDoc}
     *
     * @return EncryptionKeyListener
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $keyService  = $container->get(UserKeyService::class);
        $hydrator    = $container->get(AccountDataHydrator::class);
        $authService = $container->get(AuthenticationServiceInterface::class);
        $config      = $container->get('config');

        $listener = new EncryptionKeyListener($authService, $keyService, $hydrator);
        $listener->setCookieName($config['application']['enc_key_cookie']['name']);

        return $listener;
    }

    /**
     * Create and return EncryptionKeyListener instance
     *
     * @param ServiceLocatorInterface $container
     * @return EncryptionKeyListener
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, EncryptionKeyListener::class);
    }

}
