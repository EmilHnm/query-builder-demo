<?php
class DB
{

    private $config;
    private $connection;
    private $pdo;

    public function __construct(?array $config)
    {
        $this->config = $config;
    }

    public function connect()
    {
        if ($this->config === null) {
            throw new Exception('No config found');
        }
        $dsn = "{$this->config['DB_CONNECTION']}:host={$this->config['DB_HOST']};dbname={$this->config['DB_DATABASE']}";
        $username = $this->config['DB_USERNAME'];
        $password = $this->config['DB_PASSWORD'];
        $this->connection = new PDO($dsn, $username, $password);
    }

    public function disconnect()
    {
        $this->connection = null;
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function query($sql)
    {

        if ($this->connection === null) {
            throw new Exception('The database is not connected');
        }

        try {
            $this->pdo = $this->connection->prepare($sql);
            $this->pdo->execute();
            return $this->pdo->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
}
