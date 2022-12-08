<?php

namespace src\Builder;

include_once 'Connection.php';

class QueryBuilder
{
    private $pdo;
    protected static $table = '';
    protected static $primary_key = 'id';
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

    public static function all()
    {
        return (new self(static::$table))->get();
    }

    public static function find($id)
    {
        return (new self(static::$table))->where(static::$primary_key, '=', $id)->first();
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

    public function orWhere($column, $operator = null, $value = null)
    {
        if (is_array($column)) {
            foreach ($column as $key => $value) {
                $this->orWhere($key, $operator, $value);
            }
        } else {
            if (!empty($this->where)) {
                $this->where .= " OR {$column} {$operator} '{$value}'";
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
            throw new \Exception("Order must be ASC or DESC");
        if (is_array($column)) {
            $column = implode(',', $column);
        }
        $this->order = " ORDER BY {$column} {$order}";
        return $this;
    }

    public function limit($limit)
    {
        if (!is_numeric($limit))
            throw new \Exception("Limit must be a number");
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

    public function join($table, $first, $operator, $second)
    {
        $this->join .= " JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    public function leftJoin($table, $first, $operator, $second)
    {
        $this->join .= " LEFT JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    public function having($column, $operator = null, $value = null)
    {
        if (empty($this->grouping))
            throw new \Exception("You must use groupBy() before having()");
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
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function delete()
    {
        if (empty(static::$table))
            throw new \Exception("Table name is not set");
        $sql = "DELETE FROM " . static::$table . " {$this->where};";
        try {
            //$this->pdo->exec($sql);
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function select(...$columns)
    {
        foreach ($columns as $key => $column) {
            if (is_array($column)) {
                foreach ($column as $key => $value) {
                    $this->select .= "{$key} AS {$value}, ";
                }
            } else {
                if ($key === count($columns) - 1) {
                    $this->select .= "{$column} ";
                } else {
                    $this->select .= "{$column}, ";
                }
            }
        }
        return $this;
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
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function get()
    {
        if (empty(static::$table))
            throw new \Exception("Table name is not set");
        if (empty($this->select)) {
            $this->select = "*";
        }
        $sql = "SELECT {$this->select} FROM "
            . static::$table
            . " {$this->join} {$this->where} {$this->grouping} {$this->having} {$this->order} {$this->limit} {$this->offset};";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function count()
    {
        if (empty(static::$table))
            throw new \Exception("Table name is not set");
        if (empty($this->select)) {
            $this->select = "*";
        }
        $sql = "SELECT COUNT(*) as count FROM "
            . static::$table
            . " {$this->join} {$this->where} {$this->grouping} {$this->having} {$this->order} {$this->limit} {$this->offset};";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function first()
    {
        $result = $this->get();
        return $result[0];
    }

    public function toSql()
    {
        if (!$this->select) {
            $this->select = "*";
        }
        return "SELECT {$this->select} FROM "
            . static::$table
            . " {$this->join} {$this->where} {$this->grouping} {$this->having} {$this->order} {$this->limit} {$this->offset};";
    }
}
