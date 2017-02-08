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
    public  $email;

    public static function fromState(array $state)
    {
        return new self(
            $state['username'],
            $state['email']
        );
    }

    public function __construct($username, $email)
    {
        $this->username = $username;
        $this->email = $email;
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
