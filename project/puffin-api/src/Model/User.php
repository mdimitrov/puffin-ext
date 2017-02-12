<?php
namespace Puffin\Model;

class User
{

    /**
     * @var number
     */
    public $id = null;

    /**
     * @var string
     */
    public $username = '';

    /**
     * @var string
     */
    public $fullName = '';

    /**
     * @var string
     */
    public $password = '';

    /**
     * @var number
     */
    public $topic = 0;

    /**
     * @var string
     */
    public $role = self::USER_ROLE;


    /**
     * @var number
     */
    public $numberOfChanges = 0;

    /**
     * @var string
     */
    public  $email = null;

    const ADMIN_ROLE = 'admin';
    const USER_ROLE = 'user';

    public function setFromAssoc(array $data) {
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
        if (isset($data['username'])) {
            $this->username = $data['username'];
        }
        if (isset($data['fullName'])) {
            $this->fullName = $data['fullName'];
        }
        if (isset($data['email'])) {
            $this->email = $data['email'];
        }
        if (isset($data['password'])) {
            $this->password = $data['password'];
        }
        if (isset($data['topic'])) {
            $this->topic = $data['topic'];
        }
        if (isset($data['numberOfChanges'])) {
            $this->numberOfChanges = $data['numberOfChanges'];
        }
        if (isset($data['role'])) {
            $this->role = $data['role'];
        }
    }

    public static function fromState(array $state)
    {
        return new self(
            $state['id'],
            $state['username'],
            $state['fullName'],
            $state['email'],
            $state['password'],
            $state['topic'],
            $state['numberOfChanges'],
            $state['role']
        );
    }

    public function toAssoc($withPassword = false) {
        $result = [
            'id' => $this->id,
            'username' => $this->username,
            'fullName' => $this->fullName,
            'email' => $this->email,
            'topic' => $this->topic,
            'numberOfChanges' => $this->numberOfChanges,
            'role' => $this->role,
        ];

        if ($withPassword) {
            $result['password'] = $this->password;
        }

        return $result;
    }

    public function isAdmin() {
        return $this->role === self::ADMIN_ROLE;
    }

    public function __construct($id, $username, $fullName, $email, $password, $topic = null, $numberOfChanges = 0, $role = 'user')
    {
        $this->id = $id;
        $this->username = $username;
        $this->fullName = $fullName;
        $this->email = $email;
        $this->password = $password;
        $this->topic = $topic;
        $this->numberOfChanges = $numberOfChanges;
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
}
