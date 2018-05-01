<?php

namespace Application\Repository\Factory;

use Application\Repository\FolderRepository;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class FolderRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $table = $container->get('FoldersTable');
        return new FolderRepository($table);
    }

}
