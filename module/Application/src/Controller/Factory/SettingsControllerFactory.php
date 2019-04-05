<?php

namespace Application\Controller\Factory;


use Application\Controller\SettingsController;
use Application\Form\ChangePasswordForm;
use Application\Repository\UserRepositoryInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class SettingsControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SettingsController(
            $container->get(UserRepositoryInterface::class),
            $container->get('FormElementManager')->get(ChangePasswordForm::class)
        );
    }
}