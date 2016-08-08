<?php

namespace Application\Model;

class FolderEntity
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
    protected $name;

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
     * @return FolderEntity
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
     * @return FolderEntity
     */
    function setUserId($user_id)
    {
        $this->user_id = (int) $user_id;
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
     * @return FolderEntity
     */
    function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Load data from array
     * @param array $data
     * @return FolderEntity
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
                case 'name':
                    $this->setName($value);
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
        return array(
            'id'      => $this->id,
            'user_id' => $this->user_id,
            'name'    => $this->name,
        );
    }

}
