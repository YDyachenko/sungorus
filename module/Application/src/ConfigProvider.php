<?php

namespace Application;

use Application\Authentication;
use Application\Model;
use Application\Model\Factory as ModelFactory;
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
            'aliases' => [
                AuthenticationService::class => AuthenticationServiceInterface::class,
            ],
            'abstract_factories' => [
                StorageCacheAbstractServiceFactory::class,
                TableGatewayAbstractFactory::class,
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
                'UserModel' => ModelFactory\UserModelFactory::class,
                'FolderModel' => ModelFactory\FolderModelFactory::class,
                'AccountModel' => ModelFactory\AccountModelFactory::class,
                'AccountsDataTable' => function (ServiceLocatorInterface $sm) {
                    $dbAdapter   = $sm->get(DbAdapter::class);
                    $blockCipher = $sm->get('BlockCipher');

                    $hydrator           = new Hydrator\AccountDataDecoder($blockCipher);
                    $resultSetPrototype = new HydratingResultSet(
                            $hydrator, new Model\AccountDataEntity()
                    );

                    return new TableGateway('accounts_data', $dbAdapter, null, $resultSetPrototype);
                },
                'BlockCipher' => function () {
                    return \Zend\Crypt\BlockCipher::factory('mcrypt');
                },
                'Authentication\AuthListener' => Authentication\Factory\AuthListenerFactory::class,
                'ExportService' => Service\Factory\ExportServiceFactory::class,
                'UserKeyService' => Service\Factory\UserKeyServiceFactory::class,
                'AuthLogService' => Service\Factory\AuthLogServiceFactory::class,
                'SignupForm' => function (ServiceLocatorInterface $sm) {
                    $dbAdapter = $sm->get(DbAdapter::class);
                    return new Form\SignupForm($dbAdapter);
                }
            ],
        ];
    }

}
