<?php
namespace Puffin\Model;

class User
{

    /**
     * @var number
     */
    public $id;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var number
     */
    public $topic;

    /**
     * @var string
     */
    public $role;


    /**
     * @var number
     */
    public $numberOfChanges;

    /**
     * @var string
     */
    public  $email;

    public static function fromState(array $state)
    {
        return new self(
            $state['id'],
            $state['username'],
            $state['email'],
            $state['password'],
            $state['topic'],
            $state['numberOfChanges'],
            $state['role']
        );
    }

    public function toAssoc() {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'topic' => $this->topic,
            'numberOfChanges' => $this->numberOfChanges,
            'role' => $this->role,
        ];
    }

    public function __construct($id, $username, $email, $password, $topic, $numberOfChanges, $role)
    {
        $this->id = $id;
        $this->username = $username;
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
