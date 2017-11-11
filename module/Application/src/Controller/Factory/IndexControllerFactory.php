<?php

namespace Application\Controller\Factory;

use Application\Controller\IndexController;
use Application\Model\FolderModel;
use Application\Model\AccountModel;
use Application\Service\AuthLogService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IndexControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services       = $controllers->getServiceLocator();
        $folderModel    = $services->get(FolderModel::class);
        $accountModel   = $services->get(AccountModel::class);
        $authLogService = $services->get(AuthLogService::class);

        return new IndexController($folderModel, $accountModel, $authLogService);
    }

}
