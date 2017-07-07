<?php

namespace Application\Form\Factory;

use Application\Form\SignupForm;
use Psr\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SignupFormFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $dbAdapter = $container->get(Adapter::class);
        return new SignupForm($dbAdapter);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, SignupForm::class);
    }

}
