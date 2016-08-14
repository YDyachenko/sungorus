<?php

namespace Application\Authentication;

use Application\Service\AuthLogService;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface;
use Zend\Http\Request;
use Zend\Http\PhpEnvironment\RemoteAddress;

class LogListener implements ListenerAggregateInterface
{

    protected $listeners = array();

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var RemoteAddress
     */
    protected $remoteAddr;

    /**
     * @var AuthLogService
     */
    protected $authLogService;

    public function __construct(AuthLogService $authLogService, Request $request)
    {
        $this->authLogService = $authLogService;
        $this->request        = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(AuthenticationService::EVENT_SUCCESS, [$this, 'onSuccess']);
        $this->listeners[] = $events->attach(AuthenticationService::EVENT_FAILURE, [$this, 'onFailure']);
        $this->listeners[] = $events->attach(AuthenticationService::EVENT_DISPATCH, [$this, 'checkIpBlocked']);
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

    public function onSuccess(EventInterface $event)
    {
        $userAgent = $this->request->getHeader('User-Agent')->getFieldValue();
        $user      = $event->getParam('user');
        $ip        = $this->getIpAddress();

        $this->authLogService->logSuccess($user, $ip, $userAgent);
    }

    public function onFailure(EventInterface $event)
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
