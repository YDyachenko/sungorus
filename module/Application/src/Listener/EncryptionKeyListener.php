<?php

namespace Application\Listener;

use Application\Exception\InvalidUserKeyException;
use Application\Service\AccountDataCipher;
use Application\Service\UserKeyService;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class EncryptionKeyListener implements ListenerAggregateInterface
{

    /**
     *
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    /**
     *
     * @var UserKeyService
     */
    protected $keyService;

    /**
     *
     * @var AccountDataCipher
     */
    protected $cipher;
    protected $listeners  = [];
    protected $cookieName = 'encKey';
    protected $skipRoutes = [
        'login', 'logout', 'signup', 'encryptionKey'
    ];

    public function __construct(AuthenticationServiceInterface $authService, UserKeyService $keyService, AccountDataCipher $cipher)
    {
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
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);
    }

    /**
     * {@inheritdoc}
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function onDispatch(MvcEvent $event)
    {
        $routeMatch = $event->getRouteMatch();

        if (in_array($routeMatch->getMatchedRouteName(), $this->skipRoutes))
            return;

        $cookies = $event->getRequest()->getCookie();

        try {
            if (!isset($cookies[$this->cookieName])) {
                throw new InvalidUserKeyException('Cookie not found');
            }

            $user = $this->authService->getIdentity();
            $key  = $this->keyService->getUserKey($cookies[$this->cookieName], $user);

            $this->cipher->setKey($key);
        } catch (InvalidUserKeyException $e) {
            $controller = $event->getTarget();
            $router     = $event->getRouter();
            $response   = $event->getResponse();
            $url        = $router->assemble([], ['name' => 'encryptionKey']);
            $container  = new Container('EncryptionKey');

            $container->redirectTo = $event->getRouteMatch();


            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);

            return $response;
        }
    }

}
