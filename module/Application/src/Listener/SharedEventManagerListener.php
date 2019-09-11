<?php

namespace Application\Listener;

use Application\Authentication\AuthListener;
use Application\Controller\AuthController;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventsCapableInterface;
use Zend\Mvc\MvcEvent;

class SharedEventManagerListener extends AbstractListenerAggregate
{
    /**
     * @var AuthListener
     */
    protected $listener;

    public function __construct(AuthListener $listener)
    {
        $this->listener = $listener;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $manager = $events->getSharedManager();

        $manager->attach(
            AuthController::class,
            MvcEvent::EVENT_DISPATCH,
            function (EventInterface $event) {
                /* @var EventsCapableInterface $controller */
                $controller = $event->getTarget();

                $this->listener->attach($controller->getEventManager());
            },
            50
        );
    }
}
