<?php

namespace Application\Controller\Factory;

use Application\Controller\AccountController;
use Application\Model\FolderModel;
use Application\Model\AccountModel;
use Application\Service\FaviconService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services     = $controllers->getServiceLocator();
        $folderModel  = $services->get(FolderModel::class);
        $accountModel = $services->get(AccountModel::class);
        $iconService  = $services->get(FaviconService::class);

        return new AccountController($folderModel, $accountModel, $iconService);
    }

}
