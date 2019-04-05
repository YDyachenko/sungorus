<?php

namespace Application\Controller\Factory;

use Application\Controller\AuthController;
use Application\Form\LoginForm;
use Application\Repository\UserRepositoryInterface;
use Application\Service\UserKeyService;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config      = $container->get('config');
        $authService = $container->get(AuthenticationServiceInterface::class);
        $form        = $container->get('FormElementManager')->get(LoginForm::class);

        return new AuthController($authService, $config, $form);
    }
}
