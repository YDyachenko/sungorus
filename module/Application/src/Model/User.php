<?php

namespace Application\Model;

class User
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $login;
    
    /**
     * @var string
     */
    protected $email;
    
    /**
     * @var string
     */
    protected $password;
    
    /**
     * @var string
     */
    protected $key_hash;

    
    /**
     * Get id
     * @return int
     */
    function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     * @param int $id
     * @return User
     */
    function setId($id)
    {
        $this->id = (int) $id;
        
        return $this;
    }

    /**
     * Get login
     * @return string
     */
    function getLogin()
    {
        return $this->login;
    }

    /**
     * Set login
     * @param string $login
     * @return User
     */
    function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * Get email
     * @return string
     */
    function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     * @param string $email
     * @return User
     */
    function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get password
     * @return string
     */
    function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     * @param string $password
     * @return User
     */
    function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get key_hash
     * @return string
     */
    function getKeyHash()
    {
        return $this->key_hash;
    }

    /**
     * Set key_hash
     * @param string $value
     * @return User
     */
    function setKeyHash($value)
    {
        $this->key_hash = $value;
        return $this;
    }

    /**
     * Get cookie_key
     * @return string
     */
    function getCookieKey()
    {
        return $this->cookie_key;
    }

    /**
     * Set cookie_key
     * @param string $value
     * @return User
     */
    function setCookieKey($value)
    {
        $this->cookie_key = $value;
        return $this;
    }

    /**
     * Load data from array
     * @param array $data
     * @return User
     */
    public function exchangeArray($data)
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'id':
                    $this->setId($value);
                    break;
                case 'login':
                    $this->setLogin($value);
                    break;
                case 'email':
                    $this->setEmail($value);
                    break;
                case 'password':
                    $this->setPassword($value);
                    break;
                case 'key_hash':
                    $this->setKeyHash($value);
                    break;
                case 'cookie_key':
                    $this->setCookieKey($value);
                    break;
            }
        }

        return $this;
    }

    /**
     * Cast the object to an array
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

}
