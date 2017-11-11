<?php

namespace Application\Listener;

use Application\Service\UserKeyService;
use Application\Model\AccountModel;
use Application\Exception\InvalidUserKeyException;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class EncryptionKeyListener implements ListenerAggregateInterface
{

    protected $listeners  = [];
    protected $cookieName = 'encKey';
    protected $authService;
    protected $keyService;
    protected $accountModel;
    protected $skipRoutes = [
        'login', 'logout', 'signup', 'encryptionKey'
    ];

    public function __construct(AuthenticationServiceInterface $authService, UserKeyService $keyService, AccountModel $accountModel)
    {
        $this->authService  = $authService;
        $this->keyService   = $keyService;
        $this->accountModel = $accountModel;
    }
    
    public function setCookieName($name) {
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

            $this->accountModel->setCryptKey($key);
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
