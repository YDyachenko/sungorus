<?php

namespace Application\Listener;

use Application\Exception\InvalidUserKeyException;
use Application\Service\AccountDataCipher;
use Application\Service\UserKeyService;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class EncryptionKeyListener implements ListenerAggregateInterface
{

    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    /**
     * @var UserKeyService
     */
    protected $keyService;

    /**
     * @var AccountDataCipher
     */
    protected $cipher;
    protected $listeners  = [];
    protected $cookieName = 'encKey';
    protected $skipRoutes = ['login', 'logout', 'signup', 'encryption-key'];

    public function __construct(
        AuthenticationServiceInterface $authService,
        UserKeyService $keyService,
        AccountDataCipher $cipher
    ) {
        $this->authService = $authService;
        $this->keyService  = $keyService;
        $this->cipher      = $cipher;
    }

    public function setCookieName($name)
    {
        $this->cookieName = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);
    }

    /**
     * {@inheritdoc}
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            $events->detach($listener);
            unset($this->listeners[$index]);
        }
    }

    public function onDispatch(MvcEvent $event)
    {
        if ($event->getRequest() instanceof \Zend\Console\Request) {
            return;
        }

        $routeMatch = $event->getRouteMatch();

        if (in_array($routeMatch->getMatchedRouteName(), $this->skipRoutes)) {
            return;
        }

        $user    = $this->authService->getIdentity();
        $cookies = $event->getRequest()->getCookie();

        try {
            $key = $this->keyService->getUserKey($cookies[$this->cookieName], $user);

            $this->cipher->setKey($key);
        } catch (InvalidUserKeyException $e) {
            $router    = $event->getRouter();
            $response  = $event->getResponse();
            $url       = $router->assemble([], ['name' => 'encryption-key']);
            $container = new Container('EncryptionKey');

            $container->redirectTo = $event->getRouteMatch();

            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);

            return $response;
        }
    }
}
