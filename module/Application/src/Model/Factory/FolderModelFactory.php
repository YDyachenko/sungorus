<?php

namespace Application\Model\Factory;

use Application\Model\FolderModel;
use Psr\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FolderModelFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $table = $container->get('FoldersTable');
        return new FolderModel($table);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, FolderModel::class);
    }

}
