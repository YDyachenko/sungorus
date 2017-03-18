<?php

namespace Application\Controller\Factory;

use Application\Controller\FolderController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FolderControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services     = $controllers->getServiceLocator();
        $folderModel  = $services->get('FolderModel');
        $accountModel = $services->get('AccountModel');

        return new FolderController($folderModel, $accountModel);
    }

}
