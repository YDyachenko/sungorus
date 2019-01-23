<?php

namespace Application\Controller\Factory;

use Application\Controller\CronController;
use Application\Service\AuthLogService;
use Application\Service\UserKeyService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CronControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $keysService    = $container->get(UserKeyService::class);
        $authLogService = $container->get(AuthLogService::class);

        return new CronController($authLogService, $keysService);
    }
}
