<?php

class QueryBuilder
{
    private $pdo;
    protected static $table = '';
    private $where = '';
    private $order = '';
    private $limit = '';
    private $offset = '';
    private $grouping = '';
    private $having = '';
    private $join = '';
    private $select = '';

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public static function table($table)
    {
        static::$table = $table;
    }

    public function where($column, $operator = null, $value = null)
    {
        if (is_array($column)) {
            foreach ($column as $key => $value) {
                $this->where($key, $operator, $value);
            }
        } else {
            $this->where .= " WHERE {$column} {$operator} {$value}";
        }
        echo $this->where;
        return $this;
    }

    public function get()
    {
        $sql = "SELECT * FROM " . static::$table . $this->where;
    }
}
