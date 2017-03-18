<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Crypt\Password\Bcrypt;
use Application\Exception\UserNotFoundException;

class UserModel
{

    /**
     * Users table
     * @var TableGatewayInterface
     */
    protected $usersTable;

    /**
     * @param TableGatewayInterface $usersTable
     */
    public function __construct(TableGatewayInterface $usersTable)
    {
        $this->usersTable = $usersTable;
    }
    
    /**
     * Fet user by id
     * @param int $id
     * @return UserEntity
     * @throws UserNotFoundException
     */
    public function fetchById($id)
    {
        $id = (int)$id;
        $rowset = $this->usersTable->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new UserNotFoundException('Couldn not find user #' . $id);
        }
        
        return $row;
    }
    
    /**
     * Fetch user by identity
     * @param string $identity
     * @return UserEntity
     * @throws UserNotFoundException
     */
    public function fetchByIdentity($identity)
    {
        $identity = $identity;
        $rowset = $this->usersTable->select(array('email' => $identity));
        $row = $rowset->current();
        if (!$row) {
            throw new UserNotFoundException('Couldn not find user "' . $identity . '"');
        }
        
        return $row;
    }
    
    /**
     * Create new user
     * @param array $data
     * @return UserEntity
     */
    public function createUser(array $data)
    {
        $bcrypt = new Bcrypt();
        $user   = new UserEntity();
        $user->exchangeArray(array(
            'email'      => $data['email'],
            'login'      => '', // not yet used
            'password'   => $bcrypt->create($data['password']),
            'key_hash'   => $bcrypt->create($data['key']),
        ));

        $this->saveUser($user);
        
        return $user;
    }

    /**
     * Save user into DB
     * @param UserEntity $user
     * @return UserModel
     */
    public function saveUser(UserEntity $user)
    {
        $data = $user->getArrayCopy();
        
        $id = $data['id'];
        if ($id) {
            $this->usersTable->update($data, array('id' => $id));
        } else {
            $id = $this->usersTable->insert($data);
            $user->setId($this->usersTable->getLastInsertValue());
        }
        
        return $this;
    }

}
