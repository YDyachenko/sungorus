<?php

namespace Application;

use Application\Authentication;
use Application\Db;
use Application\Form;
use Application\Hydrator;
use Application\Listener;
use Application\Repository;
use Application\Service\FaviconService;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Authentication\Storage\StorageInterface as AuthStorage;
use Zend\Cache\Service\StorageCacheAbstractServiceFactory;
use Zend\Crypt\BlockCipher;

class ConfigProvider
{

    /**
     * Retrieve configuration for Application.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * Retrieve dependency config for Application.
     *
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            'aliases'            => [
                AuthenticationService::class => AuthenticationServiceInterface::class,
            ],
            'abstract_factories' => [
                StorageCacheAbstractServiceFactory::class,
                Db\Factory\TableGatewayAbstractFactory::class,
            ],
            'invokables'         => [
                FaviconService::class => FaviconService::class,
            ],
            'shared'             => [
                BlockCipher::class => false
            ],
            'factories'          => [
                AuthenticationServiceInterface::class            => Authentication\Factory\AuthenticationServiceFactory::class,
                AuthStorage::class                               => Authentication\Factory\StorageFactory::class,
                Hydrator\AccountDataHydrator::class              => Hydrator\Factory\AccountDataHydratorFactory::class,
                Repository\AccountRepositoryInterface::class     => Repository\Factory\AccountRepositoryFactory::class,
                Repository\AccountDataRepositoryInterface::class => Repository\Factory\AccountDataRepositoryFactory::class,
                Repository\FolderRepositoryInterface::class      => Repository\Factory\FolderRepositoryFactory::class,
                Repository\UserRepositoryInterface::class        => Repository\Factory\UserRepositoryFactory::class,
                'AccountsDataTable'                              => Db\Factory\AccountsDataTableFactory::class,
                Authentication\AuthListener::class               => Authentication\Factory\AuthListenerFactory::class,
                Service\ExportService::class                     => Service\Factory\ExportServiceFactory::class,
                Service\UserKeyService::class                    => Service\Factory\UserKeyServiceFactory::class,
                Service\AuthLogService::class                    => Service\Factory\AuthLogServiceFactory::class,
                Form\SignupForm::class                           => Form\Factory\SignupFormFactory::class,
                Listener\EncryptionKeyListener::class            => Listener\Factory\EncryptionKeyListenerFactory::class,
            ],
        ];
    }

}
