<?php

namespace Application\Controller\Factory;

use Application\Controller\AccountController;
use Application\Repository\AccountRepositoryInterface;
use Application\Repository\AccountDataRepositoryInterface;
use Application\Repository\FolderRepositoryInterface;
use Application\Service\FaviconService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services    = $controllers->getServiceLocator();
        $folders     = $services->get(FolderRepositoryInterface::class);
        $accounts    = $services->get(AccountRepositoryInterface::class);
        $data        = $services->get(AccountDataRepositoryInterface::class);
        $iconService = $services->get(FaviconService::class);

        return new AccountController($folders, $accounts, $data, $iconService);
    }

}
