<?php

namespace Application\Form\Factory;

use Application\Form\SignupForm;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\Factory\FactoryInterface;

class SignupFormFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $dbAdapter = $container->get(Adapter::class);
        return new SignupForm($dbAdapter);
    }
}
