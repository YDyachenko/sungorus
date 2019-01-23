<?php

namespace Application\Repository\Factory;

use Application\Db\TableGatewayPluginManager;
use Application\Repository\FolderRepository;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class FolderRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $tables = $container->get(TableGatewayPluginManager::class);
        $table  = $tables->get('FoldersTable');
        return new FolderRepository($table);
    }
}
