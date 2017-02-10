<?php
namespace Puffin\Model\Mapper;

use \Puffin\Model\User;

class UserMapper
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * UserMapper constructor.
     * @param $pdo \PDO
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * finds a user from storage based on ID and returns a User object
     * @param int $id
     *
     * @return \Puffin\Model\User
     */
    public function findById($id)
    {
        $sql = '
        SELECT
          username,
          password,
          email,
          role,
          theme_id as topic,
          num_of_changes as numberOfChanges
        FROM user
        WHERE id=:id';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['id' => $id]);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            throw new \InvalidArgumentException("User #$id not found");
        }

        return $this->mapRowToUser($result);
    }

    /**
     * @param $username
     * @return User
     */
    public function findByUsername($username) {
        $sql = '
        SELECT
          username,
          password,
          theme_id as topic,
          email,
          role,
          num_of_changes as numberOfChanges
        FROM user
        WHERE username=:username';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['username' => $username]);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            return $result;
        }

        return $this->mapRowToUser($result);
    }

    /**
     * @return array
     */
    public function findAll() {
        $sql = '
        SELECT
          username,
          password,
          email,
          role,
          theme_id as topic,
          num_of_changes as numberOfChanges
        FROM user';
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (!count($rows)) {
            return [];
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
            'email' => $user->getEmail(),
            'password' => $user->password
        ];

        $sql = 'INSERT INTO user (username, password, email) VALUES (:username, MD5(:password), :email)';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($userData);

        $userData['id'] = $this->pdo->lastInsertId();

        return $this->mapRowToUser($userData);
    }

    private function mapRowToUser(array $row)
    {
        return User::fromState($row);
    }
}