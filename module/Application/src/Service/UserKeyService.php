<?php

namespace Application\Service;

use Application\Model\User;
use Application\Exception\InvalidUserKeyException;
use Application\Model\EncryptionKey;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Crypt\BlockCipher;

class UserKeyService
{

    /**
     * @var TableGatewayInterface
     */
    protected $keysTable;

    /**
     * @var BlockCipher
     */
    protected $blockCipher;

    public function __construct(TableGatewayInterface $keysTable, BlockCipher $blockCipher)
    {
        $blockCipher->setBinaryOutput(true);

        $this->keysTable   = $keysTable;
        $this->blockCipher = $blockCipher;
    }

    /**
     * Add key to DB and generate cookie value
     * @param string $key User's encryption key
     * @param User $user
     * @return string
     */
    public function generateCookie($key, User $user)
    {
        $cookieKey = \Zend\Math\Rand::getString(20);
        $this->blockCipher->setKey($cookieKey);

        $this->keysTable->insert([
            'user_id' => $user->getId(),
            'key'     => $this->blockCipher->encrypt($key),
            'date'    => date('Y-m-d H:i:s')
        ]);

        $id = $this->keysTable->getLastInsertValue();
        return $id . "-" . $cookieKey;
    }

    /**
     * Get encryption key from cookie param
     * @param string $cookieValue
     * @param User $user
     * @return string
     * @throws InvalidUserKeyException
     */
    public function getUserKey($cookieValue, User $user)
    {
        if (($pos = strpos($cookieValue, '-')) < 1) {
            throw new InvalidUserKeyException('Delimiter not found');
        }
        $id = substr($cookieValue, 0, $pos);
        $this->blockCipher->setKey(substr($cookieValue, $pos + 1));

        $where  = [
            'id'      => $id,
            'user_id' => $user->getId()
        ];
        $entity = $this->keysTable->select($where)->current();
        if (!($entity instanceof EncryptionKey)) {
            throw new InvalidUserKeyException('Key not found');
        }

        $userKey = $this->blockCipher->decrypt($entity->getKey());
        if (!$userKey) {
            throw new InvalidUserKeyException('Decryption fail');
        }

        return $userKey;
    }
    
    /**
     * Delete expired user keys
     * @return int
     */
    public function deleteExpiredKeys() {
        return $this->keysTable->delete([
            '`date` < NOW() - INTERVAL 2 WEEK'
        ]);
    }

}
