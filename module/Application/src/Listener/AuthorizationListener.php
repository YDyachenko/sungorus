<?php

namespace Application\Listener;

use Zend\Authentication\AuthenticationServiceInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Router\Http\RouteMatch;

class AuthorizationListener extends AbstractListenerAggregate
{
    const SKIP_ROUTES = ['login', 'signup'];

    /**
     * @var AuthenticationServiceInterface
     */
    protected $service;

    public function __construct(AuthenticationServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'isLoggedIn'], -1);
    }

    public function isLoggedIn(MvcEvent $event)
    {
        $match = $event->getRouteMatch();

        if (! $match instanceof RouteMatch) {
            return;
        }

        if (in_array($match->getMatchedRouteName(), static::SKIP_ROUTES)) {
            return;
        }

        if ($this->service->hasIdentity()) {
            return;
        }

        $router = $event->getRouter();
        $url    = $router->assemble([], ['name' => 'login']);

        /* @var Response $response */
        $response = $event->getResponse();
        $response->getHeaders()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);

        return $response;
    }
}
