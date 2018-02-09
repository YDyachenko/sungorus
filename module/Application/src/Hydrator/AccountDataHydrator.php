<?php

namespace Application\Hydrator;

use Zend\Stdlib\Hydrator\AbstractHydrator;
use Zend\Stdlib\Hydrator\HydratorOptionsInterface;
use Zend\Crypt\BlockCipher;

class AccountDataHydrator extends AbstractHydrator
{

    /**
     * @var BlockCipher 
     */
    protected $blockCipher;

    /**
     * @param BlockCipher $blockCipher
     */
    public function __construct(BlockCipher $blockCipher)
    {
        parent::__construct();
        $blockCipher->setBinaryOutput(true);
        $this->blockCipher = $blockCipher;
    }

    /**
     * Set encryption key
     * @param string $key
     * @return AccountDataDecoder
     */
    public function setCryptKey($key)
    {
        $this->blockCipher->setKey($key);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($object)
    {
        $data         = $object->getArrayCopy();
        $jsonString   = json_encode($data['data']);
        $data['data'] = $this->blockCipher->encrypt($jsonString);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(array $data, $object)
    {
        $jsonString   = $this->blockCipher->decrypt($data['data']);
        $data['data'] = json_decode($jsonString, 1);
        $object->exchangeArray($data);

        return $object;
    }

}
