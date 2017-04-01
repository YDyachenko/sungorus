<?php

namespace Application;

use Application\Model;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter as AuthAdapter;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

class ConfigProvider
{

    /**
     * Retrieve configuration for zend-session.
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
     * Retrieve dependency config for zend-session.
     *
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            'aliases'            => [
                'Zend\Authentication\AuthenticationService' => 'AuthService'
            ],
            'abstract_factories' => [
                'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
                'Application\Service\TableGatewayAbstractFactory',
            ],
            'invokables'         => [
                'FaviconService' => 'Application\Service\FaviconService',
            ],
            'shared'             => [
                'BlockCipher' => false
            ],
            'factories'          => [
                'AuthStorage' => function (ServiceLocatorInterface $sm) {
                    $model = $sm->get('UserModel');

                    return new Authentication\Storage\SessionProxy($model);
                },
                'AuthService'        => function (ServiceLocatorInterface $sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $callback  = function ($hash, $password) {
                            $bcrypt = new \Zend\Crypt\Password\Bcrypt();
                            return $bcrypt->verify($password, $hash);
                        };

                    $authAdapter = new AuthAdapter($dbAdapter, 'users', 'email', 'password', $callback);
                    $authService = new AuthenticationService($sm->get('AuthStorage'), $authAdapter);

                    return $authService;
                },
                'UserModel' => function (ServiceLocatorInterface $sm) {
                    $table = $sm->get('UsersTable');

                    return new Model\UserModel($table);
                },
                'FolderModel' => function (ServiceLocatorInterface $sm) {
                    $foldersTable = $sm->get('FoldersTable');

                    return new Model\FolderModel($foldersTable);
                },
                'AccountsDataTable' => function (ServiceLocatorInterface $sm) {
                    $dbAdapter   = $sm->get('Zend\Db\Adapter\Adapter');
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
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new Form\SignupForm($dbAdapter);
                }
            ],
        ];
    }

}
