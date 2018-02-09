<?php

namespace Application\Controller\Factory;

use Application\Controller\IndexController;
use Application\Repository\AccountRepositoryInterface;
use Application\Repository\FolderRepositoryInterface;
use Application\Service\AuthLogService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IndexControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services       = $controllers->getServiceLocator();
        $folders        = $services->get(FolderRepositoryInterface::class);
        $accounts       = $services->get(AccountRepositoryInterface::class);
        $authLogService = $services->get(AuthLogService::class);

        return new IndexController($folders, $accounts, $authLogService);
    }

}
