<?php
namespace Puffin\Model\DataMapper;

use \Puffin\Model\User;
use Puffin\Db\SimpleDB;

class UserMapper extends SimpleDB
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * finds a user from storage based on ID and returns a User object
     * @param int $id
     *
     * @return \Puffin\Model\User
     */
    public function findById($id)
    {
        $sql = 'SELECT * FROM user WHERE id=:id';
        $result = $this->prepare($sql)->execute(['id' => $id])->fetchRowAssoc();

        if ($result === null) {
            throw new \InvalidArgumentException("User #$id not found");
        }

        return $this->mapRowToUser($result);
    }

    public function findByUsername($username) {
        $sql = 'SELECT * FROM user WHERE username=:username';
        $result = $this->prepare($sql)->execute([ 'username' => $username])->fetchRowAssoc();

        if ($result === null) {
            throw new \InvalidArgumentException("User $username not found");
        }

        return $this->mapRowToUser($result);
    }

    public function findAllByUsername($username) {
        $sql = 'SELECT * FROM user WHERE username=:username';
        $rows = $this->prepare($sql)->execute([ 'username' => $username])->fetchAllAssoc();

        if ($rows === null) {
            throw new \InvalidArgumentException("User $username not found");
        }

        $users = array_map(function($row) { return $this->mapRowToUser($row); }, $rows);

        return $users;
    }

    /**
     * @param User $user
     *
     * @return User
     */
    public function save($user) {
        $userData = [
            'username' => $user->getUsername(),
            'email' => $user->getEmail()
        ];

        $sql = 'INSERT INTO user (username, email) VALUES (:username, :email)';

        $userId = $this->prepare($sql)->execute($userData)->getLastInsertId();

        $userData['id'] = $userId;

        return $this->mapRowToUser($userData);
    }

    private function mapRowToUser(array $row)
    {
        return User::fromState($row);
    }
}