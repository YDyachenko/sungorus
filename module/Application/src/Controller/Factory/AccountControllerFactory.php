<?php

namespace Application\Controller\Factory;

use Application\Controller\AccountController;
use Application\Repository\AccountRepositoryInterface;
use Application\Repository\AccountDataRepositoryInterface;
use Application\Repository\FolderRepositoryInterface;
use Application\Service\FaviconService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AccountControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $folders     = $container->get(FolderRepositoryInterface::class);
        $accounts    = $container->get(AccountRepositoryInterface::class);
        $data        = $container->get(AccountDataRepositoryInterface::class);
        $iconService = $container->get(FaviconService::class);

        return new AccountController($folders, $accounts, $data, $iconService);
    }

}
