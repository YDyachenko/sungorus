<?php

namespace Application\Model;

class AuthLogFailureEntity
{

    /**
     * @var int
     */
    protected $ip;
    
    /**
     * @var string
     */
    protected $datetime;
    
    /**
     * @var int
     */
    protected $count;

    /**
     * Get ip
     * @return int
     */
    function getIp()
    {
        return $this->ip;
    }

    /**
     * Set ip
     * @param int $ip
     * @return AuthLogFailureEntity
     */
    function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * Get datetime
     * @return string
     */
    function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set datetime
     * @param string $datetime
     * @return AuthLogFailureEntity
     */
    function setDatetime($datetime)
    {
        $this->datetime = $datetime;
        return $this;
    }

    /**
     * Get user_agent
     * @return int
     */
    function getCount()
    {
        return $this->count;
    }

    /**
     * Set count
     * @param string $value
     * @return AuthLogFailureEntity
     */
    function setCount($value)
    {
        $this->count = $value;
        return $this;
    }

    /**
     * Load data from array
     * @param array $data
     * @return AuthLogFailureEntity
     */
    public function exchangeArray($data)
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'ip':
                    $this->setIp($value);
                    break;
                case 'datetime':
                    $this->setDatetime($value);
                    break;
                case 'count':
                    $this->setCount($value);
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
