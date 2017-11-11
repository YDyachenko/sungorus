<?php

namespace Application\Controller\Factory;

use Application\Controller\SearchController;
use Application\Model\FolderModel;
use Application\Model\AccountModel;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SearchControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services       = $controllers->getServiceLocator();
        $folderModel    = $services->get(FolderModel::class);
        $accountModel   = $services->get(AccountModel::class);

        return new SearchController($folderModel, $accountModel);
    }

}
