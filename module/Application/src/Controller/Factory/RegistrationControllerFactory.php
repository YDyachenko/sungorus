<?php

namespace Application\Controller\Factory;

use Application\Controller\RegistrationController;
use Application\Model\UserModel;
use Application\Form\SignupForm;
use Application\Service\UserKeyService;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RegistrationControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $controllers)
    {
        $services    = $controllers->getServiceLocator();
        $config      = $services->get('config');
        $form        = $services->get(SignupForm::class);
        $authService = $services->get(AuthenticationServiceInterface::class);
        $keyService  = $services->get(UserKeyService::class);
        $userModel   = $services->get(UserModel::class);

        return new RegistrationController($config, $form, $authService, $keyService, $userModel);
    }

}
