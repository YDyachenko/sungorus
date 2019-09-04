<?php

namespace Application\Service;

use Application\Exception\InvalidUserKeyException;
use Application\Model\EncryptionKey;
use Application\Model\User;
use Zend\Crypt\BlockCipher;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Math\Rand;
use Zend\Session\Container;

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

    /**
     * @var Container
     */
    protected $container;

    public function __construct(TableGatewayInterface $keysTable, BlockCipher $blockCipher)
    {
        $blockCipher->setBinaryOutput(true);

        $this->keysTable   = $keysTable;
        $this->blockCipher = $blockCipher;
    }

    /**
     * Add key to DB and generate cookie value
     * @param string $key      User's encryption key
     * @param User   $user
     * @param bool   $remember Save to DB or not
     * @return string
     */
    public function saveUserKey($key, User $user, $remember = false)
    {
        $cookieKey = Rand::getString(20);
        $this->blockCipher->setKey($cookieKey);
        $cryptedKey = $this->blockCipher->encrypt($key);

        if ($remember) {
            $this->keysTable->insert([
                'user_id' => $user->getId(),
                'key'     => $cryptedKey,
                'date'    => date('Y-m-d H:i:s'),
            ]);

            $id = $this->keysTable->getLastInsertValue();
        } else {
            $id = 0;
        }

        $this->getContainer()->cryptedKey = $cryptedKey;

        return $id . "-" . $cookieKey;
    }

    /**
     * Get encryption key from db
     * @param string $value
     * @param User   $user
     * @return string
     */
    public function getUserKey($value, User $user)
    {
        $data      = $this->parse($value);
        $container = $this->getContainer();

        if ($container->offsetExists('cryptedKey')) {
            $cryptedKey = $container->cryptedKey;
        } else {
            $where = [
                'id'      => $data['id'],
                'user_id' => $user->getId(),
            ];

            $entity = $this->keysTable->select($where)->current();
            if (! ($entity instanceof EncryptionKey)) {
                throw new InvalidUserKeyException('Key not found');
            }

            $cryptedKey = $entity->getKey();
        }

        $this->blockCipher->setKey($data['key']);
        $key = $this->blockCipher->decrypt($cryptedKey);

        if (! $key) {
            throw new InvalidUserKeyException('Decryption fail');
        }

        return $key;
    }


    public function parse($value)
    {
        if (($pos = strpos($value, '-')) < 1) {
            throw new InvalidUserKeyException('Delimiter not found');
        }

        $id  = substr($value, 0, $pos);
        $key = substr($value, $pos + 1);

        return [
            'id'  => (int)$id,
            'key' => $key,
        ];
    }

    /**
     * Delete key
     * @param string $value
     * @param User   $user
     * @return self
     */
    public function deleteKey($value, User $user)
    {
        $data = $this->parse($value);

        if ($data['id']) {
            $this->keysTable->delete([
                'id'      => $data['id'],
                'user_id' => $user->getId(),
            ]);
        }

        $this->getContainer()->offsetUnset('cryptedKey');

        return $this;
    }

    /**
     * Delete expired user keys
     * @return int
     */
    public function deleteExpiredKeys()
    {
        return $this->keysTable->delete([
            '`date` < NOW() - INTERVAL 2 WEEK',
        ]);
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        if (! $this->container) {
            $this->setContainer(new Container('EncryptionKey'));
        }

        return $this->container;
    }

    /**
     * @param Container $container
     * @return self
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }
}
