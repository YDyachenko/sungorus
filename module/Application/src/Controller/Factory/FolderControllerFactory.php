<?php

namespace Application\Controller\Factory;

use Application\Controller\FolderController;
use Application\Repository\AccountRepositoryInterface;
use Application\Repository\FolderRepositoryInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class FolderControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $folders  = $container->get(FolderRepositoryInterface::class);
        $accounts = $container->get(AccountRepositoryInterface::class);

        return new FolderController($folders, $accounts);
    }

}
