<?php

namespace Application\Model;

class EncryptionKey
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $user_id;

    /**
     * @var string
     */
    protected $key;

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
     * @return EncryptionKey
     */
    function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }

    /**
     * Get user_id
     * @return int
     */
    function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set user_id
     * @param int $user_id
     * @return EncryptionKey
     */
    function setUserId($user_id)
    {
        $this->user_id = (int) $user_id;
        return $this;
    }

    /**
     * Get key
     * @return string
     */
    function getKey()
    {
        return $this->key;
    }

    /**
     * Set key
     * @param string $key
     * @return EncryptionKey
     */
    function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Load data from array
     * @param array $data
     * @return EncryptionKey
     */
    public function exchangeArray($data)
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'id':
                    $this->setId($value);
                    break;
                case 'user_id':
                    $this->setUserId($value);
                    break;
                case 'key':
                    $this->setKey($value);
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
