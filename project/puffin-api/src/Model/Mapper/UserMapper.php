<?php
namespace Puffin\Model\Mapper;

use Puffin\Model\ModelInterface;
use \Puffin\Model\User;

class UserMapper implements MapperInterface
{
    /**
     * @var \PDO
     */
    private $pdo;

    const USER_SELECT = '
        SELECT
          id,
          username,
          full_name as fullName,
          password,
          email,
          role,
          theme_id as topic,
          num_of_changes as numberOfChanges,
          is_blocked as isBlocked
        FROM user';

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
        if (!isset($id)) {
            return null;
        }

        $select = self::USER_SELECT;
        $sql = "
            $select
            WHERE id=:id
        ";
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['id' => $id]);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return $this->mapRowToObject($result);
    }

    /**
     * @param $username
     * @return User
     */
    public function findByUsername($username) {

        if (!isset($username)) {
            return null;
        }

        $select = self::USER_SELECT;
        $sql = "
            $select
            WHERE username=:username
            LIMIT 1;
        ";
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['username' => $username]);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return $this->mapRowToObject($result);
    }

    /**
     * @param $email
     * @return User
     */
    public function findByEmail($email) {

        if (!isset($email)) {
            return null;
        }

        $select = self::USER_SELECT;
        $sql = "
            $select
            WHERE email=:email
            LIMIT 1;
        ";
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['email' => $email]);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return $this->mapRowToObject($result);
    }

    /**
     * @param int $limit
     * @param int $skip
     * @param string $direction
     * @return array
     */
    public function findAll($limit = 20, $skip = 0, $direction = 'DESC') {
        $sql = self::USER_SELECT . " ORDER BY username $direction LIMIT :skip , :limit;";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
        $statement->bindValue(':skip', (int) $skip, \PDO::PARAM_INT);
        $statement->execute();
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (!count($rows)) {
            return [];
        }

        $users = array_map(function($row) { return $this->mapRowToObject($row); }, $rows);

        return $users;
    }

    /**
     * @param boolean $withPassword
     * @param int $limit
     * @param int $skip
     * @param string $direction
     * @return array
     */
    public function findAllAssoc($withPassword = false, $limit = 20, $skip = 0, $direction = 'DESC') {
        $sql = self::USER_SELECT . " ORDER BY username $direction LIMIT :skip , :limit;";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
        $statement->bindValue(':skip', (int) $skip, \PDO::PARAM_INT);

        $statement->execute();
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (!count($rows)) {
            return [];
        }

        $users = array_map(function($row) use ($withPassword) { return $this->mapRowToObject($row)->toAssoc($withPassword); }, $rows);

        return $users;
    }

    /**
     * @param boolean $withPassword
     * @param $query
     * @return array
     */
    public function findAllSearchAssoc($withPassword = false, $query) {
        $sql = self::USER_SELECT . " 
        WHERE username LIKE :query  
        OR full_name LIKE :query 
        OR email LIKE :query  
        OR role LIKE :query 
        ORDER BY username DESC";
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['query' => "%$query%"]);
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (!count($rows)) {
            return [];
        }

        $users = array_map(function($row) use ($withPassword) { return $this->mapRowToObject($row)->toAssoc($withPassword); }, $rows);

        return $users;
    }

    public function updatePassword($userId, $data) {
        $userData = [
            'id' => $userId,
            'password' => $data['newPassword']
        ];

        $sql = 'UPDATE user SET password=md5(:password) WHERE id=:id';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($userData);
        return $statement->rowCount();
    }

    public function updateInfo($userId, $data) {
       $userData = [
            'id' => $userId,
            'fullName' => $data['fullName'],
            'email' => $data['email']
        ];

        $sql = 'UPDATE user SET full_name=:fullName, email=:email WHERE id=:id';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($userData);
        return $statement->rowCount();
    }

    public function updateRole($userId, $data) {
        $userData = [
            'id' => $userId,
            'role' => $data['role']
        ];

        $sql = 'UPDATE user SET role=:role WHERE id=:id';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($userData);
        return $statement->rowCount();
    }

    public function updateBlocked($userId, $data) {
        $userData = [
            'id' => $userId,
            'isBlocked' => $data['isBlocked']
        ];

        $sql = 'UPDATE user SET is_blocked=:isBlocked WHERE id=:id';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($userData);
        return $statement->rowCount();
    }

    public function delete($userId) {
        $userData = [
            'id' => $userId
        ];

        $sql = 'DELETE FROM user WHERE id=:id';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($userData);
        return $statement->rowCount();
    }

    /**
     * @param ModelInterface $model
     *
     * @return User
     */
    public function save(ModelInterface $model) {
        $userData = $model->toAssoc();

        $sql = '
        INSERT INTO user (id, username, full_name, email, password, theme_id, num_of_changes, role, is_blocked)
        VALUES (:id, :username, :fullName, :email, MD5(:password), :topic, :numOfChanges, :role, :is_blocked)
        ON DUPLICATE KEY UPDATE
            id=VALUES(id),
            username=VALUES(username),
            full_name=VALUES(full_name),
            email=VALUES(email),
            password=VALUES(password),
            theme_id=VALUES(theme_id),
            num_of_changes=VALUES(num_of_changes),
            role=VALUES(role),
            is_blocked=VALUES(is_blocked);
        ';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($userData);

        $userData['id'] = $this->pdo->lastInsertId();

        return $this->mapRowToObject($userData);
    }

    public function mapRowToObject(array $row)
    {
        return User::fromState($row);
    }
}