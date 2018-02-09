<?php

namespace Application\Controller\Factory;

use Application\Controller\FolderController;
use Application\Repository\AccountRepositoryInterface;
use Application\Repository\FolderRepositoryInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FolderControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services = $controllers->getServiceLocator();
        $folders  = $services->get(FolderRepositoryInterface::class);
        $accounts = $services->get(AccountRepositoryInterface::class);

        return new FolderController($folders, $accounts);
    }

}
