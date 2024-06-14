<?php

/**
 * Zelasli Framework
 * 
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli
 */

namespace Zelasli;
use PDO;
use RuntimeException;
use SQLite3;

class Database {
    protected static $instance;

    protected $conn = null;

    protected function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public static function connect($config = [], $shared = true) {
        
        if (empty($config) && ($default = Helpers::config('database.default'))) {
            $config = Helpers::config("database.connections.$default");
        }

        if (empty($config['driver'])) {
            throw new RuntimeException('Driver not specified');
        }

        $pdoParams = [];
        if ($config['driver'] === 'sqlite') {
            $datastoreDir = Helpers::varPath('datastore/database');
            $dbfile = $datastoreDir . DIRECTORY_SEPARATOR . $config['name'];

            if (!file_exists($dbfile)) {
                new SQLite3($dbfile);
            }

            $pdoParams['dsn'] = $config['driver'] . ':' . $datastoreDir . 
                DIRECTORY_SEPARATOR . $config['name'];
        } else {
            $dsn = $config['driver'] . ':';
            $dsn .= join(';', [
                'host=' . $config['host'],
                'port=' . $config['port'],
                'dbname=' . $config['name']
            ]);

            $pdoParams = [
                'dsn' => $dsn,
                'username' => $config['username'],
                'password' => $config['password']
            ];
        }

        if ($shared) {
            return static::$instance = new self(new PDO(...$pdoParams));
        } else {
            return new self(new PDO(...$pdoParams));
        }

        return self::$instance;
    }

    public function exec($sql) {
        return $this->conn->exec($sql);
    }

    public function query($sql, $params = [])
    {
        $prepStmt = $this->conn->prepare($sql);

        foreach ($params as $param => $value) {
            $prepStmt->bindValue(':' . $param, $value);
        }

        $prepStmt->execute();

        return $prepStmt;
    }
}
