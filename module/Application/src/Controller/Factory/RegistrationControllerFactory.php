<?php

namespace Application\Controller\Factory;

use Application\Controller\RegistrationController;
use Application\Form\SignupForm;
use Application\Repository\UserRepositoryInterface;
use Application\Service\UserKeyService;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class RegistrationControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config      = $container->get('config');
        $forms       = $container->get('FormElementManager');
        $form        = $forms->get(SignupForm::class);
        $authService = $container->get(AuthenticationServiceInterface::class);
        $keyService  = $container->get(UserKeyService::class);
        $users       = $container->get(UserRepositoryInterface::class);

        return new RegistrationController($config, $form, $authService, $keyService, $users);
    }
}
