<?php

namespace Application\Authentication;

use Zend\Authentication\AuthenticationService as ZendAuthService;
use Zend\Authentication\Adapter;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Session\Container as SessionContainer;

class AuthenticationService extends ZendAuthService implements EventManagerAwareInterface
{

    const EVENT_SUCCESS  = 'success';
    const EVENT_FAILURE  = 'failure';
    const EVENT_DISPATCH = 'dispatch';

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * {@inheritdoc}
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(Adapter\AdapterInterface $adapter = null)
    {
        if (!$adapter) {
            if (!$adapter = $this->getAdapter()) {
                throw new \Application\Exception\RuntimeException('An adapter must be set or passed prior to calling authenticate()');
            }
        }
        $result = $adapter->authenticate();

        /**
         * ZF-7546 - prevent multiple successive calls from storing inconsistent results
         * Ensure storage has clean state
         */
        if ($this->hasIdentity()) {
            $this->clearIdentity();
        }

        if ($result->isValid()) {
            SessionContainer::getDefaultManager()->regenerateId();
            $this->getStorage()->write($result->getIdentity());
            $row    = $adapter->getResultRowObject(array('id'));
            $params = array(
                'user_id' => $row->id,
            );
            $this->eventManager->trigger(self::EVENT_SUCCESS, $this, $params);
        } else {
            $this->eventManager->trigger(self::EVENT_FAILURE, $this);
        }

        return $result;
    }

}
