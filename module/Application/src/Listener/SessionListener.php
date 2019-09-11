<?php

namespace Application\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Session\Exception\ExceptionInterface as SessionException;
use Zend\Session\ManagerInterface;
use Zend\Session\ValidatorChain;

class SessionListener extends AbstractListenerAggregate
{
    /**
     * @var ManagerInterface
     */
    protected $manager;


    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'startSession'], 1000);
    }

    public function startSession(MvcEvent $event)
    {
        try {
            $this->manager->start();
        } catch (SessionException $ex) {
            $this->manager->destroy(['clear_storage' => true]);
            $this->manager->setValidatorChain(new ValidatorChain($this->manager->getStorage()));

            return $this->redirectResponse($event, 'login');
        }
    }

    /**
     * @param MvcEvent $event
     * @param string   $routeName
     * @return Response
     */
    protected function redirectResponse(MvcEvent $event, $routeName)
    {
        $router   = $event->getRouter();
        $url      = $router->assemble([], ['name' => $routeName]);
        $response = $event->getResponse();

        $event->stopPropagation();

        $response->setStatusCode(302)
                 ->getHeaders()->addHeaderLine('Location', $url);

        return $response;
    }
}
