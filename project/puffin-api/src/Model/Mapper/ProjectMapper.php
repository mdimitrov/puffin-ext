<?php
namespace Puffin\Model\Mapper;

use Puffin\Model\ModelInterface;
use Puffin\Model\Project;

class ProjectMapper implements MapperInterface
{

    /**
     * @var \PDO
     */
    private $pdo;

    const PROJECT_SELECT = '
        SELECT
          p.id as id,
          u.username as username,
          u.full_name as fullName,
          version,
          p.theme as topic,
          status,
          comment,
          uploaded as dateUploaded
        FROM projects as p
        INNER JOIN user as u ON user_id=u.id';

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
     * @return \Puffin\Model\Project
     */
    public function findById($id)
    {
        if (!isset($id)) {
            return null;
        }

        $select = self::PROJECT_SELECT;
        $sql = "
            $select
            WHERE p.id=:id
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
     * @param int $limit
     * @param int $skip
     * @param string $direction
     * @return array Project
     */
    public function findAllByUsername($username, $limit = 20, $skip = 0, $direction = 'DESC') {

        if (!isset($username)) {
            return null;
        }

        $select = self::PROJECT_SELECT;
        $sql = "
            $select
             WHERE u.username=:username
             ORDER BY uploaded $direction LIMIT :skip , :limit;
        ";
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['username' => $username]);
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (!count($rows)) {
            return [];
        }

        $projects = array_map(function($row) { return $this->mapRowToObject($row); }, $rows);

        return $projects;
    }

    /**
     * @param int $limit
     * @param int $skip
     * @param string $direction
     * @return array
     */
    public function findAll($limit = 20, $skip = 0, $direction = 'DESC') {
        $sql = self::PROJECT_SELECT . " ORDER BY uploaded {$direction} LIMIT :skip , :limit;";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
        $statement->bindValue(':skip', (int) $skip, \PDO::PARAM_INT);
        $statement->execute();
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (!count($rows)) {
            return [];
        }

        $projects = array_map(function($row) { return $this->mapRowToObject($row); }, $rows);

        return $projects;
    }

    /**
     * @param int $limit
     * @param int $skip
     * @param string $direction
     * @return array
     */
    public function findAllAssoc($limit = 20, $skip = 0, $direction = 'DESC') {
        $sql = self::PROJECT_SELECT . " ORDER BY uploaded {$direction} LIMIT :skip , :limit;";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
        $statement->bindValue(':skip', (int) $skip, \PDO::PARAM_INT);
        $statement->execute();
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (!count($rows)) {
            return [];
        }

        $projects = array_map(function($row) { return $this->mapRowToObject($row)->toAssoc(); }, $rows);

        return $projects;
    }

    /**
     * @param $query
     * @return array
     */
    public function findAllSearchAssoc($query) {
        $sql = self::PROJECT_SELECT . " 
        WHERE username LIKE :query  
        OR u.full_name LIKE :query 
        OR theme LIKE :query 
        ORDER BY username DESC";
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['query' => "%$query%"]);
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (!count($rows)) {
            return [];
        }

        $projects = array_map(function($row) { return $this->mapRowToObject($row)->toAssoc(); }, $rows);

        return $projects;
    }


    public function updateStatus($projectId, $data) {
        $projectData = [
            'id' => $projectId,
            'status' => $data['status']
        ];
        $sql = 'UPDATE projects SET status=:status WHERE id=:id';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($projectData);
        return $statement->rowCount();
    }

    public function mapRowToObject(array $row)
    {
        return Project::fromState($row);
    }

    public function save(ModelInterface $model)
    {
        // TODO: Implement save() method.
    }
}