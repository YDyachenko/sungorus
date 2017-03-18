<?php

namespace Application\Controller\Plugin;

use Application\Exception\InvalidUserKeyException;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class CheckUserEncryptionKey extends AbstractPlugin
{

    protected $keyService;
    protected $accountModel;

    public function __construct($keyService, $accountModel, $config)
    {
        $this->keyService   = $keyService;
        $this->accountModel = $accountModel;
        $this->cookie_name = $config['application']['enc_key_cookie']['name'];
    }

    /**
     * Callback for "dispatch" event
     * Check encryption key in cookies. Setup key in the model.
     * @param MvcEvent $event
     */
    public function __invoke(MvcEvent $event)
    {
        $cookie = $this->controller->getRequest()->getCookie();

        try {
            if (!isset($cookie[$this->cookie_name])) {
                throw new InvalidUserKeyException('Cookie not found');
            }
            $user = $this->controller->identity();
            $key  = $this->keyService->getUserKey($cookie[$this->cookie_name], $user);
            
            $this->accountModel->setCryptKey($key);
        } catch (InvalidUserKeyException $e) {
            $container = new Container('EncryptionKey');

            $container->redirectTo = $event->getRouteMatch();

            return $this->controller->redirect()->toRoute('encryptionKey');
        }
    }

}
