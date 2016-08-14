<?php

namespace Application;

use Application\Model;
use Application\Authentication\AuthenticationService;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter as AuthAdapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Http\Request as HttpRequest;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;


class Module implements
    BootstrapListenerInterface,
    ConfigProviderInterface,
    ServiceProviderInterface,
    AutoloaderProviderInterface,
    ConsoleUsageProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function onBootstrap(EventInterface $e)
    {
        $em = $e->getApplication()->getEventManager();

        if ($e->getRequest() instanceof HttpRequest) {
            $em->attach(MvcEvent::EVENT_ROUTE, [$this, 'onRoute'], -100);

            $sm = $e->getApplication()->getServiceManager();
            $logListener = $sm->get('Authentication\LogListener');
            $sm->get('AuthService')->getEventManager()->attachAggregate($logListener);

            $sessionManager = $sm->get('SessionManager');
            try {
                $sessionManager->start();
            } catch (\Exception $ex) {
                $sessionManager->destroy();
                return $this->redirectToLoginPage($e);
            }
        }
    }

    /**
     * Callback for "route" event
     * Redirect to login page if user is not authenticated
     * @param MvcEvent $e
     * @return \Zend\Http\Response|null
     */
    public function onRoute(MvcEvent $e)
    {
        $skipRoutes = ['login', 'signup'];
        $match      = $e->getRouteMatch();

        if (!$match instanceof RouteMatch) {
            return;
        }

        if (in_array($match->getMatchedRouteName(), $skipRoutes)) {
            return;
        }

        $sm = $e->getApplication()->getServiceManager();

        if ($sm->get('AuthService')->hasIdentity()) {
            return;
        }

        return $this->redirectToLoginPage($e);
    }

    protected function redirectToLoginPage(MvcEvent $e)
    {
        $router = $e->getRouter();
        $url    = $router->assemble([], ['name' => 'login']);

        $response = $e->getResponse();
        $response->getHeaders()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [];
 
        $configPaths = [
            __DIR__ . '/config/module.config.php',
            __DIR__ . '/config/routes.php',
            __DIR__ . '/config/controllers.php',
            __DIR__ . '/config/tablegateways.php',
        ];

        foreach ($configPaths as $path) {
            $config = \Zend\Stdlib\ArrayUtils::merge($config, include $path);
        }

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceConfig()
    {
        return [
            'factories'   => [
                'SessionManager'                      => 'Zend\Session\Service\SessionManagerFactory',
                'Zend\Session\Config\ConfigInterface' => 'Zend\Session\Service\SessionConfigFactory',
                'AuthStorage' => function (ServiceLocatorInterface $sm) {
                    $model = $sm->get('UserModel');

                    return new Authentication\Storage\SessionProxy($model);
                },
                'AuthService' => function (ServiceLocatorInterface $sm) {
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
                'Authentication\LogListener' => function (ServiceLocatorInterface $sm) {
                    $request        = $sm->get('Request');
                    $authLogService = $sm->get('AuthLogService');

                    return new Authentication\LogListener($authLogService, $request);
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
    
    /**
     * {@inheritdoc}
     */
    public function getConsoleUsage(Console $console)
    {
        return [
            'cron clearKeysTable'       => '[CRONJOB] Remove out-of-date encryption keys from DB',
            'cron clearLogFailureTable' => '[CRONJOB] Remove out-of-date entries from authentication failure log'
        ];
    }

}
