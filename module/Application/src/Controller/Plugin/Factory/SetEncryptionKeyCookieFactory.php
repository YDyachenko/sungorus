<?php

namespace Application\Controller\Plugin\Factory;

use Application\Controller\Plugin\SetEncryptionKeyCookie;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class SetEncryptionKeyCookieFactory implements FactoryInterface
{

    /**
     * {@inheritDoc}
     *
     * @return SetEncryptionKeyCookie
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        return new SetEncryptionKeyCookie($config);
    }

}
