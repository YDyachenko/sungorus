<?php

namespace Application\Repository\Factory;

use Application\Repository\FolderRepository;
use Psr\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FolderRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $table = $container->get('FoldersTable');
        return new FolderRepository($table);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, FolderRepository::class);
    }

}
