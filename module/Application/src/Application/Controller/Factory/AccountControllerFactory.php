<?php

namespace Application\Controller\Factory;

use Application\Controller\AccountController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services     = $controllers->getServiceLocator();
        $folderModel  = $services->get('FolderModel');
        $accountModel = $services->get('AccountModel');
        $iconService  = $services->get('FaviconService');

        return new AccountController($folderModel, $accountModel, $iconService);
    }

}
