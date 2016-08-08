<?php

namespace Application\Controller\Plugin;

use Zend\Http\Header\SetCookie;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class SetEncryptionKeyCookie extends AbstractPlugin
{

    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Set cookie with encryption key
     * @param type $value Cookie value
     * @param type $expires Expires time
     */
    public function __invoke($value, $expires = null)
    {

        $cfg = $this->config['application']['enc_key_cookie'];

        $path = $this->controller->getRequest()->getBasePath() . '/';

        $cookie = new SetCookie($cfg['name'], $value, $expires, $path, null, $cfg['secure'], true);
        $this->controller->getResponse()->getHeaders()->addHeader($cookie);
    }

}
