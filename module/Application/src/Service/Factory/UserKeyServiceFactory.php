<?php

namespace Application\Service\Factory;

use Application\Db\TableGatewayPluginManager;
use Application\Service\UserKeyService;
use Interop\Container\ContainerInterface;
use Zend\Crypt\BlockCipher;
use Zend\ServiceManager\Factory\FactoryInterface;

class UserKeyServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $blockCipher = $container->get(BlockCipher::class);
        $tables      = $container->get(TableGatewayPluginManager::class);
        $table       = $tables->get('EncryptionKeysTable');
        return new UserKeyService($table, $blockCipher);
    }
}
