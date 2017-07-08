<?php

namespace Application;

use Application\Authentication;
use Application\Db;
use Application\Form;
use Application\Model;
use Application\Service\FaviconService;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Authentication\Storage\StorageInterface as AuthStorage;
use Zend\Cache\Service\StorageCacheAbstractServiceFactory;

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
            'aliases' => [
                AuthenticationService::class => AuthenticationServiceInterface::class,
            ],
            'abstract_factories' => [
                StorageCacheAbstractServiceFactory::class,
                Db\Factory\TableGatewayAbstractFactory::class,
            ],
            'invokables' => [
                FaviconService::class => FaviconService::class,
            ],
            'shared' => [
                'BlockCipher' => false
            ],
            'factories' => [
                AuthenticationServiceInterface::class => Authentication\Factory\AuthenticationServiceFactory::class,
                AuthStorage::class => Authentication\Factory\StorageFactory::class,
                'UserModel' => Model\Factory\UserModelFactory::class,
                'FolderModel' => Model\Factory\FolderModelFactory::class,
                'AccountModel' => Model\Factory\AccountModelFactory::class,
                'AccountsDataTable' => Db\Factory\AccountsDataTableFactory::class,
                'Authentication\AuthListener' => Authentication\Factory\AuthListenerFactory::class,
                'ExportService' => Service\Factory\ExportServiceFactory::class,
                'UserKeyService' => Service\Factory\UserKeyServiceFactory::class,
                'AuthLogService' => Service\Factory\AuthLogServiceFactory::class,
                'SignupForm' => Form\Factory\SignupFormFactory::class,
            ],
        ];
    }

}
