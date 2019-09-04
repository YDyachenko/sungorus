<?php


namespace Application\Db;

use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\ServiceManager\AbstractPluginManager;

class TableGatewayPluginManager extends AbstractPluginManager
{

    public function validate($instance)
    {
        if ($instance instanceof TableGatewayInterface) {
            return;
        }

        throw new InvalidServiceException('This is not a valid service!');
    }
}
