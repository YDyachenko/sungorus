<?php

namespace Application\Authentication;

use Zend\EventManager\Event;
use Zend\Stdlib\RequestInterface as Request;

class AuthEvent extends Event
{

    const EVENT_AUTHENTICATION         = 'authentication';
    const EVENT_AUTHENTICATION_SUCCESS = 'authentication.success';
    const EVENT_AUTHENTICATION_FAILURE = 'authentication.failure';

    /**
     * Get request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set request
     *
     * @param Request $request
     * @return MvcEvent
     */
    public function setRequest(Request $request)
    {
        $this->setParam('request', $request);
        $this->request = $request;
        return $this;
    }
}
