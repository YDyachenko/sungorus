<?php

namespace Application\Controller\Factory;

use Application\Controller\FolderController;
use Application\Model\FolderModel;
use Application\Model\AccountModel;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FolderControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services     = $controllers->getServiceLocator();
        $folderModel  = $services->get(FolderModel::class);
        $accountModel = $services->get(AccountModel::class);

        return new FolderController($folderModel, $accountModel);
    }

}
