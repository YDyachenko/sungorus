<?php

namespace Application\Controller\Factory;

use Application\Controller\SearchController;
use Application\Repository\AccountRepositoryInterface;
use Application\Repository\FolderRepositoryInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class SearchControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $folders  = $container->get(FolderRepositoryInterface::class);
        $accounts = $container->get(AccountRepositoryInterface::class);

        return new SearchController($folders, $accounts);
    }

}
