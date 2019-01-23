<?php

namespace Application\Repository;

use Application\Exception\UserNotFoundException;
use Application\Model\User;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Crypt\Password\Bcrypt;

class UserRepository implements UserRepositoryInterface
{

    /**
     * Users table
     * @var TableGatewayInterface
     */
    protected $table;

    /**
     * @param TableGatewayInterface $usersTable
     */
    public function __construct(TableGatewayInterface $table)
    {
        $this->table = $table;
    }

    /**
     * {@inheritdoc}
     */
    public function findById($id)
    {
        $id     = (int)$id;
        $rowset = $this->table->select(['id' => $id]);
        $row    = $rowset->current();
        if (! $row) {
            throw new UserNotFoundException('Couldn not find user #' . $id);
        }

        return $row;
    }

    /**
     * {@inheritdoc}
     */
    public function findByIdentity($identity)
    {
        $identity = $identity;
        $rowset   = $this->table->select(['email' => $identity]);
        $row      = $rowset->current();
        if (! $row) {
            throw new UserNotFoundException('Couldn not find user "' . $identity . '"');
        }

        return $row;
    }

    /**
     * {@inheritdoc}
     */
    public function createUser(array $data)
    {
        $bcrypt = new Bcrypt();
        $user   = new User();
        $user->exchangeArray([
            'email'    => $data['email'],
            'login'    => '', // not yet used
            'password' => $bcrypt->create($data['password']),
            'key_hash' => $bcrypt->create($data['key']),
        ]);

        $this->save($user);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function save(User $user)
    {
        $data = $user->getArrayCopy();

        $id = $data['id'];
        if ($id) {
            $this->table->update($data, ['id' => $id]);
        } else {
            $id = $this->table->insert($data);
            $user->setId($this->table->getLastInsertValue());
        }

        return $this;
    }
}
