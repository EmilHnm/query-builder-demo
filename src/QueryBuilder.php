<?php

include 'Connection.php';



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

    public function __construct($table = '')
    {
        self::$table = $table;
        $this->pdo = Connection::connect();
    }

    public static function table($table)
    {
        static::$table = $table;
        $static = new static($table);
        return $static;
    }

    public static function RAW($sql)
    {
        return $sql;
    }

    public function where($column, $operator = null, $value = null)
    {
        if (is_array($column)) {
            foreach ($column as $key => $value) {
                $this->where($key, $operator, $value);
            }
        } else {
            if ($this->where) {
                $this->where .= " AND {$column} {$operator} '{$value}'";
            } else {
                $this->where .= " WHERE {$column} {$operator} '{$value}'";
            }
        }
        return $this;
    }

    public function orderBy($column, $order = 'ASC')
    {
        $order = strtoupper($order);
        if (!in_array($order, ['ASC', 'DESC']))
            throw new Exception("Order must be ASC or DESC");
        if (is_array($column)) {
            $column = implode(',', $column);
        }
        $this->order = " ORDER BY {$column} {$order}";
        return $this;
    }

    public function limit($limit)
    {
        if (!is_numeric($limit))
            throw new Exception("Limit must be a number");
        $this->limit = " LIMIT {$limit}";
        return $this;
    }

    public function groupBy($column)
    {
        if (is_array($column)) {
            $column = implode(',', $column);
        }
        $this->grouping = " GROUP BY {$column}";
        return $this;
    }

    public function having($column, $operator = null, $value = null)
    {
        if (empty($this->grouping))
            throw new Exception("You must use groupBy() before having()");
        if (is_array($column)) {
            foreach ($column as $key => $value) {
                $this->having($key, $operator, $value);
            }
        } else {
            if ($this->having) {
                $this->having .= " AND {$column} {$operator} '{$value}'";
            } else {
                $this->having .= " HAVING {$column} {$operator} '{$value}'";
            }
        }
        return $this;
    }

    public function insert($data)
    {
        $insertData = '';
        if (count($data) == count($data, COUNT_RECURSIVE)) {
            $columns = implode(',', array_keys($data));
            $values = implode(',', array_values($data));
            $insertData = "({$values}),";
        } else {
            foreach ($data as $key => $values) {
                $columns = implode(',', array_keys($values));
                $value = implode(',', array_values($values));
                $insertData .= "({$value}),";
            }
        }
        $sql = "INSERT INTO " . static::$table . " ({$columns}) VALUES {$insertData};";
        try {
            //$this->pdo->exec($sql);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function update($data)
    {
        $updateData = '';
        foreach ($data as $key => $value) {
            $updateData .= "{$key} = '{$value}',";
        }
        $sql = "UPDATE " . static::$table . " SET {$updateData} {$this->where};";
        echo $sql;
        try {
            //$this->pdo->exec($sql);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function get()
    {
        if (empty(static::$table))
            throw new Exception("Table name is not set");
        if (!$this->select) {
            $this->select = "*";
        }
    }
}
