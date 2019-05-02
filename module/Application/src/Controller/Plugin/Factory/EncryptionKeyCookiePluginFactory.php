<?php

namespace Application\Controller\Plugin\Factory;

use Application\Controller\Plugin\EncryptionKeyCookiePlugin;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class EncryptionKeyCookiePluginFactory implements FactoryInterface
{

    /**
     * {@inheritDoc}
     *
     * @return EncryptionKeyCookiePlugin
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        return new EncryptionKeyCookiePlugin($config['application']['enc_key_cookie']);
    }
}
