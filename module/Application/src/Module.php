<?php

namespace Application;

use Application\Controller;
use Application\Listener;
use Application\Authentication;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;
use Zend\Router\Http\RouteMatch;
use Zend\Http\Request as HttpRequest;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Crypt\BlockCipher;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Session\SessionManager;

class Module implements BootstrapListenerInterface, ConfigProviderInterface, ConsoleUsageProviderInterface, ServiceProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function onBootstrap(EventInterface $e)
    {
        $em = $e->getApplication()->getEventManager();

        if ($e->getRequest() instanceof HttpRequest) {
            $sm = $e->getApplication()->getServiceManager();

            $em->attach(MvcEvent::EVENT_ROUTE, function ($e) use ($sm) {
                $sessionManager = $sm->get(SessionManager::class);
                try {
                    $sessionManager->start();
                } catch (\Zend\Session\Exception\RuntimeException $ex) {
                    $sessionManager->destroy();
                    $e->stopPropagation();
                    return $this->redirectToLoginPage($e);
                }
            }, 1000);
            $em->attach(MvcEvent::EVENT_ROUTE, [$this, 'onRoute'], -100);
            $aggregate = $sm->get(Listener\EncryptionKeyListener::class);
            $aggregate->attach($em);

            $sharedManager = $em->getSharedManager();
            $sharedManager->attach(Controller\AuthController::class, MvcEvent::EVENT_DISPATCH, function($e) use ($sm) {
                $controller = $e->getTarget();
                $aggregate   = $sm->get(Authentication\AuthListener::class);
                $aggregate->attach($controller->getEventManager());
            }, 2);
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

        if ($sm->get(AuthenticationServiceInterface::class)->hasIdentity()) {
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
        $provider = new ConfigProvider();
        $config   = [
            'service_manager' => $provider->getDependencyConfig()
        ];

        $configs = [
            'module.config.php',
            'routes.php',
            'controllers.php',
            'tablegateways.php',
        ];

        foreach ($configs as $file) {
            $path   = __DIR__ . '/../config/' . $file;
            $config = \Zend\Stdlib\ArrayUtils::merge($config, include $path);
        }

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceConfig()
    {
        return [
            'factories' => [
                BlockCipher::class => function () {
                    return \Zend\Crypt\BlockCipher::factory('openssl');
                },
        ]];
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
