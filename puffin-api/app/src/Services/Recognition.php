<?php

namespace Puffin\Services;

use Puffin\Model\DataMapper\UserMapper;

class Recognition {

    private function __construct() {

    }

    public static function authenticate($username, $password) {
        $um = new UserMapper();

        // check if user exits and get his data
        if ($user = $um->findByUsername($username)) {
            // check if hash of provided password matches the hash in the database
            if (!password_verify($password, $user->password)) {
                return false;
            }
        } else {
            return false;
        }

        return $user;
    }

}
