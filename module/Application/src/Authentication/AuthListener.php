<?php

namespace Application\Authentication;

use Application\Service\AuthLogService;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface;
use Zend\Http\Request;
use Zend\Http\PhpEnvironment\RemoteAddress;

class AuthListener implements ListenerAggregateInterface
{

    protected $listeners = array();

    /**
     * @var RemoteAddress
     */
    protected $remoteAddr;

    /**
     * @var AuthLogService
     */
    protected $authLogService;

    public function __construct(AuthLogService $authLogService)
    {
        $this->authLogService = $authLogService;
    }

    /**
     * {@inheritdoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(AuthEvent::EVENT_AUTHENTICATION, [$this, 'checkIpBlocked']);
        $this->listeners[] = $events->attach(AuthEvent::EVENT_AUTHENTICATION_SUCCESS, [$this, 'onSuccess']);
        $this->listeners[] = $events->attach(AuthEvent::EVENT_AUTHENTICATION_FAILURE, [$this, 'onFailure']);
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

    public function onSuccess(AuthEvent $event)
    {
        $request   = $event->getRequest();
        $userAgent = $request->getHeader('User-Agent')->getFieldValue();
        $user      = $event->getTarget()->identity();
        $ip        = $this->getIpAddress();

        $this->authLogService->logSuccess($user, $ip, $userAgent);
    }

    public function onFailure(AuthEvent $event)
    {
        $this->authLogService->logFailure($this->getIpAddress());
    }

    public function checkIpBlocked(EventInterface $event)
    {
        $ip = $this->getIpAddress();

        if ($this->authLogService->isIpBlocked($ip)) {
            $event->stopPropagation(true);
            return [
                'message' => 'Your IP address has been blocked'
            ];
        }
    }

    protected function getIpAddress()
    {
        if (!$this->remoteAddr) {
            $this->remoteAddr = new RemoteAddress();
        }

        return $this->remoteAddr->getIpAddress();
    }

}
