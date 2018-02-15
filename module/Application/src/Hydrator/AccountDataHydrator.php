<?php

namespace Application\Hydrator;

use Application\Service\AccountDataCipher;
use Zend\Stdlib\Hydrator\AbstractHydrator;

class AccountDataHydrator extends AbstractHydrator
{

    /**
     * @var AccountDataCipher 
     */
    protected $cipher;

    /**
     * @param AccountDataCipher $cipher
     */
    public function __construct(AccountDataCipher $cipher)
    {
        parent::__construct();
        $this->cipher = $cipher;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($object)
    {
        $data         = $object->getArrayCopy();
        $jsonString   = json_encode($data['data']);
        $data['data'] = $this->cipher->encrypt($jsonString);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(array $data, $object)
    {
        $jsonString   = $this->cipher->decrypt($data['data']);
        $data['data'] = json_decode($jsonString, 1);
        $object->exchangeArray($data);

        return $object;
    }

}
