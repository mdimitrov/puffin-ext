<?php
namespace Puffin\Model\Mapper;

use Puffin\Model\ModelInterface;
use \Puffin\Model\PasswordRecoveryCode;

class PasswordRecoveryCodeMapper implements MapperInterface
{
    /**
     * @var \PDO
     */
    private $pdo;

    const SELECT = '
        SELECT
          user_id as userId,
          code
        FROM password_recovery_code';

    /**
     * PasswordRecoveryCodeMapper constructor.
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
     * @return PasswordRecoveryCode
     */
    public function findById($id)
    {
        if (!isset($id)) {
            return null;
        }

        $select = self::SELECT;
        $sql = "
            $select
            WHERE user_id=:id
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
     * @return array
     */
    public function findAll() {
        $sql = self::SELECT;
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (!count($rows)) {
            return [];
        }

        return array_map(function($row) { return $this->mapRowToObject($row); }, $rows);
    }

    /**
     * @return array
     */
    public function findAllAssoc() {
        $sql = self::SELECT;
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (!count($rows)) {
            return [];
        }

        return array_map(function($row) { return $this->mapRowToObject($row)->toAssoc(); }, $rows);
    }

    public function mapRowToObject(array $row)
    {
        return PasswordRecoveryCode::fromState($row);
    }

    /**
     * @param ModelInterface $model
     * @return PasswordRecoveryCode
     */
    public function save(ModelInterface $model)
    {
        $data = $model->toAssoc();

        $sql = '
        INSERT INTO password_recovery_code (user_id, code) 
        VALUES (:userId, :code) 
        ON DUPLICATE KEY UPDATE code=VALUES(code)
        ';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($data);

        return $model;
    }
}