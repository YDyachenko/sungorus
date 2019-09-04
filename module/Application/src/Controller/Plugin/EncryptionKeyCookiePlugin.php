<?php

namespace Application\Controller\Plugin;

use Zend\Http\Header\SetCookie;
use Zend\Http\Request;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class EncryptionKeyCookiePlugin extends AbstractPlugin
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var boolean
     */
    protected $secure;

    /**
     * @var int
     */
    protected $lifetime;

    public function __construct($options)
    {
        $this->setOptions($options);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * @param int $lifetime
     * @return self
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * @param bool $secure
     * @return self
     */
    public function setSecure($secure)
    {
        $this->secure = $secure;

        return $this;
    }

    /**
     * @param array $options
     * @return self
     */
    public function setOptions(array $options)
    {
        if (! empty($options)) {
            foreach ($options as $key => $value) {
                switch (strtolower($key)) {
                    case 'name':
                        $this->setName($value);
                        break;
                    case 'secure':
                        $this->setSecure($value);
                        break;
                    case 'lifetime':
                        $this->setLifetime($value);
                        break;
                }
            }
        }

        return $this;
    }

    /**
     * Send cookie with encryption key
     * @param string  $value Cookie value
     * @param boolean $remember
     * @return self
     */
    public function send($value, $remember = false)
    {
        $expires = $remember ? (time() + $this->getLifetime()) : null;

        return $this->sendCookie($value, $expires);
    }

    /**
     * Delete cookie
     * @return self
     */
    public function delete()
    {
        return $this->sendCookie('', time());
    }

    protected function sendCookie($value, $expires)
    {
        $path   = $this->controller->getRequest()->getBasePath() . '/';
        $cookie = new SetCookie($this->getName(), $value, $expires, $path, null, $this->isSecure(), true);

        $this->controller->getResponse()->getHeaders()->addHeader($cookie);

        return $this;
    }

    /**
     * Get cookie value
     * @return string
     */
    public function getValue()
    {
        /* @var Request $request */
        $request = $this->controller->getRequest();
        $cookies = $request->getCookie();

        return $cookies[$this->getName()];
    }
}
