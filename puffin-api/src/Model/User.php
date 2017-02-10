<?php
namespace Puffin\Model;

class User
{
    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public  $email;

    public static function fromState(array $state)
    {
        return new self(
            $state['username'],
            $state['email'],
            $state['password']
        );
    }

    public function toAssoc() {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }

    public function __construct($username, $email, $password)
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
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
