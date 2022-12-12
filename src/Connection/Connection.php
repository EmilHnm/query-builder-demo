<?php

namespace Hoangm\Query\Connection;

class Connection
{

    private  $connection;

    public function __construct()
    {
        $this->connection = self::connect();
    }

    public static function connect()
    {
        $config = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/config.ini');
        $dsn = $config['DB_CONNECTION'] . ":" . "host=" . $config['DB_HOST'] . ";dbname=" . $config['DB_DATABASE'];
        $username = $config['DB_USERNAME'];
        $password = $config['DB_PASSWORD'];
        return new \PDO($dsn, $username, $password);
    }
}
