<?php

namespace Application\Controller\Factory;

use Application\Controller\IndexController;
use Application\Repository\AccountRepositoryInterface;
use Application\Repository\FolderRepositoryInterface;
use Application\Service\AuthLogService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $folders        = $container->get(FolderRepositoryInterface::class);
        $accounts       = $container->get(AccountRepositoryInterface::class);
        $authLogService = $container->get(AuthLogService::class);

        return new IndexController($folders, $accounts, $authLogService);
    }

}
