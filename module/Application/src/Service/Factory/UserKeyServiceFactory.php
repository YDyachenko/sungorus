<?php

namespace Application\Service\Factory;

use Application\Service\UserKeyService;
use Psr\Container\ContainerInterface;
use Zend\Crypt\BlockCipher;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserKeyServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $blockCipher = $container->get(BlockCipher::class);
        $table       = $container->get('EncryptionKeysTable');
        return new UserKeyService($table, $blockCipher);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, UserKeyService::class);
    }

}
