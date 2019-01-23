<?php

namespace Application\Model;

class Folder
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     * @param int $id
     * @return Folder
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
     * @param int $user_id
     * @return Folder
     */
    public function setUserId($user_id)
    {
        $this->user_id = (int)$user_id;
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
     * @return Folder
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Load data from array
     * @param array $data
     * @return Folder
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
        return [
            'id'      => $this->id,
            'user_id' => $this->user_id,
            'name'    => $this->name,
        ];
    }
}
