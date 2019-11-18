<?php

namespace Application\Authentication;

use Zend\Authentication\Result;
use Zend\EventManager\Event;
use Zend\Stdlib\RequestInterface;

class AuthEvent extends Event
{

    const EVENT_AUTHENTICATE_POST = 'authenticate.post';

    /**
     * @var RequestInterface
     */
    protected $request;


    /**
     * @var Result
     */
    protected $result;

    /**
     * Get request
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set request
     * @param RequestInterface $request
     * @return self
     */
    public function setRequest(RequestInterface $request)
    {
        $this->setParam('request', $request);
        $this->request = $request;

        return $this;
    }

    /**
     * Get authentication result
     * @return Result
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set authentication result
     * @param Result $result
     * @return self
     */
    public function setResult(Result $result)
    {
        $this->setParam('result', $result);
        $this->result = $result;

        return $this;
    }
}
