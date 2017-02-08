<?php

namespace Puffin\Db;

class PDOWrapper {
    
    private static $instance = null;

    /**
     * @return \PDO
     */
    public static function getInstance() {
        if (self::$instance == null) {
            $config = require BULLET_APP_ROOT . '/config/local.php';

            self::$instance = new \PDO(
                "mysql:host={$config['database']['host']};dbname={$config['database']['dbname']}",
                $config['database']['user'],
                $config['database']['pass'],
                $config['pdo']
            );
        }
        return self::$instance;
    }

}