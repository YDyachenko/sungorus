<?php

namespace Application\Controller;

use Zend\Console\Request as ConsoleRequest;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;

class CronController extends AbstractActionController
{

    /**
     *
     * @var \Zend\Db\TableGateway\TableGatewayInterface
     */
    protected $logTable;
    
    /**
     *
     * @var \Zend\Db\TableGateway\TableGatewayInterface
     */
    protected $keysTable;
    
    /**
     *
     * @var \Zend\Config\Config
     */
    protected $config;

    public function __construct($config, $logTable, $keysTable)
    {
        $this->logTable  = $logTable;
        $this->keysTable = $keysTable;
        $this->config    = $config;
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
        $rows = $this->keysTable->delete([
            '`date` < NOW() - INTERVAL 2 WEEK'
        ]);

        return "Removed $rows row(s)\n";
    }

    public function clearLogFailureTableAction()
    {
        $blocktime = $this->config['application']['authentication']['logListener']['blocktime'];

        $rows = $this->logTable->delete(array(
            '`datetime` < NOW() - INTERVAL ? SECOND' => $blocktime
        ));

        return "Removed $rows row(s)\n";
    }

}
