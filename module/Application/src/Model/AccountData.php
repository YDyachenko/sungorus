<?php

namespace Application\Model;

class AccountData
{

    /**
     * @var int
     */
    protected $account_id;

    /**
     * @var array
     */
    protected $data;

    /**
     * Get account_id
     * @return int
     */
    public function getAccountId()
    {
        return $this->account_id;
    }

    /**
     * Set account_id
     * @param int $id
     * @return AccountData
     */
    public function setAccountId($id)
    {
        $this->account_id = (int)$id;
        return $this;
    }

    /**
     * Get data
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set data
     * @param array $data
     * @return AccountData
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Load data from array
     * @param array $data
     * @return AccountData
     */
    public function exchangeArray($data)
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'account_id':
                    $this->setAccountId($value);
                    break;
                case 'data':
                    $this->setData($value);
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
            'account_id' => $this->account_id,
            'data'       => $this->data,
        ];
    }
}
