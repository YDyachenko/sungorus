<?php

namespace Application\Service\Factory;

use Application\Service\UserKeyService;
use Interop\Container\ContainerInterface;
use Zend\Crypt\BlockCipher;
use Zend\ServiceManager\Factory\FactoryInterface;

class UserKeyServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $blockCipher = $container->get(BlockCipher::class);
        $table       = $container->get('EncryptionKeysTable');
        return new UserKeyService($table, $blockCipher);
    }

}
