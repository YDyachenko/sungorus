<?php


namespace Application\Controller\Factory;

use Application\Controller\EncryptionKeyController;
use Application\Form\EncryptionKeyForm;
use Application\Service\UserKeyService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class EncryptionKeyControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new EncryptionKeyController(
            $container->get('FormElementManager')->get(EncryptionKeyForm::class),
            $container->get(UserKeyService::class)
        );
    }
}
