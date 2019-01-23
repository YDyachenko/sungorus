<?php

namespace Application\Model;

use Application\Exception\BadMethodCallException;

class Account
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
     * @var int
     */
    protected $folder_id;

    /**
     * @var int
     */
    protected $favorite;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $date_created;

    /**
     * @var string
     */
    protected $date_modified;

    /**
     * Get id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     * @return Account
     */
    public function setId($id)
    {
        $this->id = (int)$id;
        return $this;
    }

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
     * @return Account
     */
    public function setUserId($userId)
    {
        $this->user_id = (int)$userId;
        return $this;
    }

    /**
     * Get folder_id
     * @return int
     */
    public function getFolderId()
    {
        return $this->folder_id;
    }

    /**
     * Set folder_id
     * @return Account
     */
    public function setFolderId($folderId)
    {
        $this->folder_id = (int)$folderId;
        return $this;
    }

    /**
     * Get favorite flag
     * @return int
     */
    public function getFavorite()
    {
        return $this->favorite;
    }

    /**
     * Set favorite flag
     * @param int $value
     * @return Account
     */
    public function setFavorite($value)
    {
        $this->favorite = $value ? 1 : 0;
        return $this;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     * @param string $name
     * @return Account
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get date created
     * @return string
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Set date created
     * @param string $value
     * @return Account
     * @throws BadMethodCallException
     */
    public function setDateCreated($value)
    {
        if (is_null($this->date_created)) {
            $this->date_created = $value;
        } else {
            throw new BadMethodCallException('Changing creation date is not allowed');
        }
        return $this;
    }

    /**
     * Get date modified
     * @return string
     */
    public function getDateModified()
    {
        return $this->date_modified;
    }

    /**
     * Set date modified
     * @param string $value
     * @return Account
     */
    public function setDateModified($value)
    {
        $this->date_modified = $value;
        return $this;
    }

    /**
     * Load data from array
     * @param array $data
     * @return Account
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
                case 'folder_id':
                    $this->setFolderId($value);
                    break;
                case 'favorite':
                    $this->setFavorite($value);
                    break;
                case 'name':
                    $this->setName($value);
                    break;
                case 'date_created':
                    $this->setDateCreated($value);
                    break;
                case 'date_modified':
                    $this->setDateModified($value);
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
