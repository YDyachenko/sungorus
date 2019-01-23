<?php

namespace Application\Model;

class AuthLogSuccess
{

    /**
     * @var int
     */
    protected $user_id;

    /**
     * @var int
     */
    protected $ip;

    /**
     * @var string
     */
    protected $datetime;

    /**
     * @var string
     */
    protected $user_agent;


    /**
     * Get user_id
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set user_id
     * @param int $id
     * @return AuthLogSuccess
     */
    public function setUserId($id)
    {
        $this->user_id = (int)$id;

        return $this;
    }

    /**
     * Get ip
     * @return int
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set ip
     * @param int $ip
     * @return AuthLogSuccess
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * Get datetime
     * @return string
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set datetime
     * @param string $datetime
     * @return AuthLogSuccess
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
        return $this;
    }

    /**
     * Get user_agent
     * @return string
     */
    public function getUserAgent()
    {
        return $this->user_agent;
    }

    /**
     * Set user_agent
     * @param string $userAgent
     * @return AuthLogSuccess
     */
    public function setUserAgent($userAgent)
    {
        $this->user_agent = $userAgent;
        return $this;
    }

    /**
     * Load data from array
     * @param array $data
     * @return AuthLogSuccess
     */
    public function exchangeArray($data)
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'user_id':
                    $this->setUserId($value);
                    break;
                case 'ip':
                    $this->setIp($value);
                    break;
                case 'datetime':
                    $this->setDatetime($value);
                    break;
                case 'user_agent':
                    $this->setUserAgent($value);
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
