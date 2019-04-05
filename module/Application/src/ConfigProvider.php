<?php

namespace Application;

use Application\Authentication;
use Application\Db;
use Application\Form;
use Application\Hydrator;
use Application\Listener;
use Application\Repository;
use Application\Service;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Authentication\Storage\StorageInterface as AuthStorage;
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
            'service_manager' => $this->getDependencyConfig(),
            'hydrators'       => $this->getHydratorConfig(),
            'form_elements'   => $this->getFormConfig(),
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
            ],
            'invokables'         => [
                Service\FaviconService::class => Service\FaviconService::class,

            ],
            'shared'             => [
                BlockCipher::class => false,
            ],
            'factories'          => [
                Db\TableGatewayPluginManager::class              => Db\Factory\TableGatewayPluginManagerFactory::class,
                Service\AccountDataCipher::class                 => Service\Factory\AccountDataCipherFactory::class,
                AuthenticationServiceInterface::class            => Authentication\Factory\AuthenticationServiceFactory::class,
                AuthStorage::class                               => Authentication\Factory\StorageFactory::class,
                Repository\AccountRepositoryInterface::class     => Repository\Factory\AccountRepositoryFactory::class,
                Repository\AccountDataRepositoryInterface::class => Repository\Factory\AccountDataRepositoryFactory::class,
                Repository\FolderRepositoryInterface::class      => Repository\Factory\FolderRepositoryFactory::class,
                Repository\UserRepositoryInterface::class        => Repository\Factory\UserRepositoryFactory::class,
                Authentication\AuthListener::class               => Authentication\Factory\AuthListenerFactory::class,
                Service\ExportService::class                     => Service\Factory\ExportServiceFactory::class,
                Service\UserKeyService::class                    => Service\Factory\UserKeyServiceFactory::class,
                Service\AuthLogService::class                    => Service\Factory\AuthLogServiceFactory::class,
                Listener\EncryptionKeyListener::class            => Listener\Factory\EncryptionKeyListenerFactory::class,
            ],
        ];
    }

    public function getHydratorConfig()
    {
        return [
            'factories' => [
                Hydrator\AccountDataHydrator::class => Hydrator\Factory\AccountDataHydratorFactory::class,
            ],
        ];
    }

    public function getFormConfig()
    {
        return [
            'factories' => [
                Form\SignupForm::class => Form\Factory\SignupFormFactory::class,
            ],
        ];
    }
}
