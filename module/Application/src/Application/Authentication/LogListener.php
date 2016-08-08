<?php

namespace Application\Authentication;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface;
use Zend\Http\Request;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Expression;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Application\Exception\InvalidArgumentException;

class LogListener implements ListenerAggregateInterface
{

    protected $listeners = array();

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var TableGatewayInterface
     */
    protected $successTable;

    /**
     * @var TableGatewayInterface
     */
    protected $failureTable;

    /**
     * @var RemoteAddress
     */
    protected $remoteAddr;

    /**
     * @var array 
     */
    protected $options = array(
        'maxfailures' => 5,
        'blocktime'   => 86400,
    );

    public function __construct(Request $request, TableGatewayInterface $successTable, TableGatewayInterface $failureTable, $options = array())
    {
        $this->request      = $request;
        $this->successTable = $successTable;
        $this->failureTable = $failureTable;
        
        $this->setOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(AuthenticationService::EVENT_SUCCESS, array($this, 'onSuccess'));
        $this->listeners[] = $events->attach(AuthenticationService::EVENT_FAILURE, array($this, 'onFailure'));
        $this->listeners[] = $events->attach(AuthenticationService::EVENT_DISPATCH, array($this, 'checkIpBlocked'));
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

        $set = array(
            'user_id'    => $event->getParam('user_id'),
            'ip'         => $this->ip2long($this->getIpAddress()),
            'user_agent' => substr($userAgent, 0, 255)
        );

        $this->successTable->insert($set);
    }

    public function onFailure(EventInterface $event)
    {
        $ip = $this->ip2long($this->getIpAddress());

        $rowset = $this->failureTable->select(array('ip' => $ip));

        if ($rowset->count()) {
            $this->failureTable->update(array(
                'count'    => new Expression('count + 1'),
                'datetime' => new Expression('now()')
            ), array('ip' => $ip));
        } else {
            $this->failureTable->insert(array(
                'ip'       => $ip,
                'count'    => 1,
                'datetime' => new Expression('now()')
            ));
        }
    }

    public function checkIpBlocked(EventInterface $event)
    {
        $ip    = $this->ip2long($this->getIpAddress());
        $where = array(
            'ip'                                     => $ip,
            'count >= ?'                             => $this->options['maxfailures'],
            '`datetime` > now() - INTERVAL ? SECOND' => $this->options['blocktime']
        );

        $rowset = $this->failureTable->select($where);

        if ($rowset->count()) {
            $event->stopPropagation(true);
            return array(
                'message' => 'Your IP address has been blocked'
            );
        }
    }

    protected function getIpAddress()
    {
        if (!$this->remoteAddr) {
            $this->remoteAddr = new RemoteAddress();
        }

        return $this->remoteAddr->getIpAddress();
    }

    protected function ip2long($ip)
    {
        return sprintf('%u', ip2long($ip));
    }
    
    /**
     * Set options
     * @param array|Traversable $options
     * @throws InvalidArgumentException
     */
    public function setOptions($options) {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new InvalidArgumentException(
                'The options parameter must be an array or a Traversable'
            );
        }
        
        foreach ($options as $name => $value) {
            if (isset($this->options[$name])) {
                $this->options[$name] = $value;
            }
        }
    }
    
    /**
     * Return the specified option
     * @param string $option
     * @return NULL|mixed
     */
    public function getOption($option)
    {
        if (!isset($this->options[$option])) {
            return null;
        }

        return $this->options[$option];
    }

}
