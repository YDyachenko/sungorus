<?php

namespace Application;

use Application\Authentication\Service\AuthenticationServiceFactory;
use Application\Authentication\Service\StorageFactory as AuthStorageFactory;
use Application\Model;
use Application\Service\FaviconService;
use Application\Service\TableGatewayAbstractFactory;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Authentication\Storage\StorageInterface as AuthStorage;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Cache\Service\StorageCacheAbstractServiceFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

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
                TableGatewayAbstractFactory::class,
            ],
            'invokables'         => [
                FaviconService::class => FaviconService::class,
            ],
            'shared'             => [
                'BlockCipher' => false
            ],
            'factories'          => [
                AuthenticationServiceInterface::class => AuthenticationServiceFactory::class,
                AuthStorage::class                    => AuthStorageFactory::class,
                'UserModel'                           => function (ServiceLocatorInterface $sm) {
                    $table = $sm->get('UsersTable');

                    return new Model\UserModel($table);
                },
                'FolderModel'        => function (ServiceLocatorInterface $sm) {
                    $foldersTable = $sm->get('FoldersTable');

                    return new Model\FolderModel($foldersTable);
                },
                'AccountsDataTable' => function (ServiceLocatorInterface $sm) {
                    $dbAdapter   = $sm->get(DbAdapter::class);
                    $blockCipher = $sm->get('BlockCipher');

                    $hydrator           = new Hydrator\AccountDataDecoder($blockCipher);
                    $resultSetPrototype = new HydratingResultSet(
                        $hydrator, new Model\AccountDataEntity()
                    );

                    return new TableGateway('accounts_data', $dbAdapter, null, $resultSetPrototype);
                },
                'AccountModel' => function (ServiceLocatorInterface $sm) {
                    $accountsTable = $sm->get('AccountsTable');
                    $dataTable     = $sm->get('AccountsDataTable');

                    return new Model\AccountModel($accountsTable, $dataTable);
                },
                'BlockCipher' => function () {
                    return \Zend\Crypt\BlockCipher::factory('mcrypt');
                },
                'Authentication\AuthListener' => function (ServiceLocatorInterface $sm) {
                    $authLogService = $sm->get('AuthLogService');

                    return new Authentication\AuthListener($authLogService);
                },
                'ExportService' => function (ServiceLocatorInterface $sm) {
                    $folderModel  = $sm->get('FolderModel');
                    $accountModel = $sm->get('AccountModel');

                    return new Service\ExportService($folderModel, $accountModel);
                },
                'UserKeyService' => function (ServiceLocatorInterface $sm) {
                    $blockCipher = $sm->get('BlockCipher');
                    $table       = $sm->get('EncryptionKeysTable');

                    return new Service\UserKeyService($table, $blockCipher);
                },
                'AuthLogService' => function (ServiceLocatorInterface $sm) {
                    $config       = $sm->get('Config');
                    $successTable = $sm->get('AuthLogSuccessTable');
                    $failureTable = $sm->get('AuthLogFailureTable');

                    return new Service\AuthLogService($config, $successTable, $failureTable);
                },
                'SignupForm' => function (ServiceLocatorInterface $sm) {
                    $dbAdapter = $sm->get(DbAdapter::class);
                    return new Form\SignupForm($dbAdapter);
                }
            ],
        ];
    }

}
