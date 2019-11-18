<?php

namespace Application\Listener;

use Application\Authentication\AuthFailureListener;
use Application\Authentication\AuthSuccessListener;
use Application\Controller\AuthController;
use Psr\Container\ContainerInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventsCapableInterface;
use Zend\Mvc\MvcEvent;

class SharedEventManagerListener extends AbstractListenerAggregate
{
    /**
     * @var ContainerInterface[]
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $manager = $events->getSharedManager();

        $manager->attach(AuthController::class, MvcEvent::EVENT_DISPATCH, [$this, 'checkIpBlocked'], 100);
        $manager->attach(AuthController::class, MvcEvent::EVENT_DISPATCH, [$this, 'attachAuthListeners'], 99);
    }

    public function attachAuthListeners(MvcEvent $event)
    {
        /* @var EventsCapableInterface $controller */
        $controller = $event->getTarget();
        $events     = $controller->getEventManager();

        $this->container->get(AuthSuccessListener::class)->attach($events);
        $this->container->get(AuthFailureListener::class)->attach($events);
    }

    public function checkIpBlocked(MvcEvent $event)
    {
        $listener = $this->container->get(AuthFailureListener::class);

        return $listener->checkIpBlocked($event);
    }
}
