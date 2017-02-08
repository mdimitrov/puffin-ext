<?php

namespace Puffin;

class Session {


    public function __construct($options) {
        $name = $options['name'];
        $lifetime = $options['lifetime'];
        $path = $options['path'];
        $domain = $options['domain'];
        $secure = $options['secure'];

        if (strlen($name) < 1) {
            $name = '_sess';
        }
        session_name($name);
        session_set_cookie_params($lifetime, $path, $domain, $secure, true);
        session_start();
    }

    public function destroySession() {
        session_destroy();
    }

    public function getSessionId() {
        return session_id();
    }

    public function saveSession() {
        session_write_close();
    }

    public function __get($name) {
        return $_SESSION[$name];
    }

    public function __set($name, $value) {
        $_SESSION[$name] = $value;
    }
}