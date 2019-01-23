<?php


namespace Application\Db;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\Db\TableGateway\TableGatewayInterface;

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
