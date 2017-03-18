<?php

namespace Application\Controller\Factory;

use Application\Controller\SearchController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SearchControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services       = $controllers->getServiceLocator();
        $folderModel    = $services->get('FolderModel');
        $accountModel   = $services->get('AccountModel');

        return new SearchController($folderModel, $accountModel);
    }

}
