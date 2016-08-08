<?php

namespace Application\Model;

use Application\Exception\BadMethodCallException;

class AccountEntity
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
    function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     * @return AccountEntity
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
     * @return AccountEntity
     */
    function setUserId($userId)
    {
        $this->user_id = (int) $userId;
        return $this;
    }

    /**
     * Get folder_id
     * @return int
     */
    function getFolderId()
    {
        return $this->folder_id;
    }

    /**
     * Set folder_id
     * @return AccountEntity
     */
    function setFolderId($folderId)
    {
        $this->folder_id = (int) $folderId;
        return $this;
    }
    
    /**
     * Get favorite flag
     * @return int
     */
    function getFavorite()
    {
        return $this->favorite;
    }

    /**
     * Set favorite flag
     * @param int $value
     * @return AccountEntity
     */
    function setFavorite($value)
    {
        $this->favorite = $value ? 1 : 0;
        return $this;
    }

    /**
     * Get name
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     * @param string $name
     * @return AccountEntity
     */
    function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Get date created
     * @return string
     */
    function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Set date created
     * @param string $value
     * @return AccountEntity
     * @throws BadMethodCallException
     */
    function setDateCreated($value)
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
    function getDateModified()
    {
        return $this->date_modified;
    }

    /**
     * Set date modified
     * @param string $value
     * @return AccountEntity
     */
    function setDateModified($value)
    {
        $this->date_modified = $value;
        return $this;
    }

    /**
     * Load data from array
     * @param array $data
     * @return AccountEntity
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
