<?php


namespace Application\Authentication;

use Application\Service\AuthLogService;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\PhpEnvironment\RemoteAddress;

class AuthSuccessListener extends AbstractListenerAggregate
{
    /**
     * @var AuthLogService
     */
    protected $logs;

    /**
     * @var AuthenticationServiceInterface
     */
    protected $authenticationService;

    public function __construct(AuthLogService $logs, AuthenticationServiceInterface $service)
    {
        $this->logs = $logs;

        $this->authenticationService = $service;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $events->attach(AuthEvent::EVENT_AUTHENTICATE_POST, [$this, 'onAuthenticatePost'], $priority);
    }

    public function onAuthenticatePost(AuthEvent $event)
    {
        $result = $event->getResult();
        if (! $result->isValid()) {
            return;
        }

        $request   = $event->getRequest();
        $userAgent = $request->getHeader('User-Agent')->getFieldValue();
        $user      = $this->authenticationService->getIdentity();
        $ip        = (new RemoteAddress())->getIpAddress();

        $this->logs->logSuccess($user, $ip, $userAgent);
    }
}
