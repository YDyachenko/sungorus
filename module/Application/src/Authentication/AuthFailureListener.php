<?php

namespace Application\Authentication;

use Application\Service\AuthLogService;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class AuthFailureListener extends AbstractListenerAggregate
{

    /**
     * @var AuthLogService
     */
    protected $logs;

    public function __construct(AuthLogService $logs)
    {
        $this->logs = $logs;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $events->attach(AuthEvent::EVENT_AUTHENTICATE_POST, [$this, 'onAuthenticatePost'], $priority);
    }

    public function onAuthenticatePost(AuthEvent $event)
    {
        if ($event->getResult()->isValid()) {
            return;
        }

        $this->logs->logFailure($this->getIpAddress());
    }

    public function checkIpBlocked(MvcEvent $event)
    {
        $ip = $this->getIpAddress();

        if ($this->logs->isIpBlocked($ip)) {
            $event->stopPropagation(true);

            $model = new ViewModel();
            $model->setTemplate('error/blocked');

            $view = $event->getViewModel();
            $view->setTemplate('layout/error')
                 ->addChild($model);
        }
    }

    protected function getIpAddress()
    {
        return (new RemoteAddress())->getIpAddress();
    }
}
