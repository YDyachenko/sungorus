<?php

namespace Application\Controller\Factory;

use Application\Controller\IndexController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IndexControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services     = $controllers->getServiceLocator();
        $folderModel  = $services->get('FolderModel');
        $accountModel = $services->get('AccountModel');
        $authLogTable = $services->get('AuthLogSuccessTable');

        return new IndexController($folderModel, $accountModel, $authLogTable);
    }

}
