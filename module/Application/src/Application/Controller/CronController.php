<?php

namespace Application\Controller;

use Zend\Console\Request as ConsoleRequest;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;

class CronController extends AbstractActionController
{

    /**
     *
     * @var \Application\Service\AuthLogService
     */
    protected $authLogService;

    /**
     *
     * @var \Application\Service\UserKeyService
     */
    protected $keysService;

    public function __construct($authLogService, $keysService)
    {
        $this->authLogService = $authLogService;
        $this->keysService    = $keysService;
    }

    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $events->attach('dispatch', function ($e) {
            $request = $e->getRequest();
            if (!$request instanceof ConsoleRequest) {
                throw new \RuntimeException(sprintf(
                    '%s can only be executed in a console environment', __CLASS__
                ));
            }
        }, 100);
        return $this;
    }

    public function clearKeysTableAction()
    {
        $rows = $this->keysService->deleteExpiredKeys();

        return "Removed $rows row(s)\n";
    }

    public function clearLogFailureTableAction()
    {
        $rows = $this->authLogService->deleteOldFailures();

        return "Removed $rows row(s)\n";
    }

}
