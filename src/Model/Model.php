<?php

namespace Hoangm\Query\Model;

use JsonSerializable;
use Hoangm\Query\Trait\HasAttribute;
use Hoangm\Query\Trait\HasTimestamps;
use Hoangm\Query\Builder\QueryBuilder;
use Hoangm\Query\Trait\HideAttributes;
use Hoangm\Query\Connection\Connection;
use Hoangm\Query\Exeptions\ModelNotFoundExeption;

abstract class Model implements JsonSerializable
{
    use HasAttribute;
    use HasTimestamps;
    use HideAttributes;


    private ?\PDO $pdo;
    private ?QueryBuilder $builder;
    protected bool $exists = false;

    protected static string $table = '';
    protected static string $primary_key = 'id';
    protected static ?Model $instance = null;

    public function __construct()
    {
        $this->pdo = Connection::connect();
        $this->builder = new QueryBuilder(static::$table);
    }

    public static function __callStatic($name, $arguments)
    {
        return (new static)->{"pre".$name}(...$arguments); 
    }

    public function __call($name, $arguments)
    {
        return $this->{"pre".$name}(...$arguments);
    }
    
    // Model creation and update

    public static function precreate(array $data)
    {
        $insertData = '';

        $data = array_map(function ($value) {
            return "'{$value}'";
        }, $data);
        $columns = implode(',', array_keys($data));
        $values = implode(',', array_values($data));
        $insertData = "({$values})";
        
        $sql = "INSERT INTO " . static::$table . " ({$columns}) VALUES {$insertData};";
        try {
            $stmt = (new static)->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function preupdate(array $attributes = [], array $options = []) {
        if (! $this->exists) {
            if(empty($options)) 
                return $this->builder->update($attributes) ? true : false;
            else {
                $sql = "UPDATE " . static::$table . " SET ";
                foreach ($options as $key => $value) {
                    $sql .= "{$key} = '{$value}'";
                    if($key !== array_key_last($options))
                        $sql .= ", ";
                }
                $sql .= " WHERE ";
                foreach ($attributes as $key => $value) {
                    $sql .= "{$key} = '{$value}'";
                    if($key !== array_key_last($attributes))
                        $sql .= " AND ";
                }
                $sql .= ";";
                try {
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute();
                    return $stmt->rowCount() ? true : false;
                } catch (\PDOException $e) {
                    throw new \Exception($e->getMessage());
                }
            }
        } else {
            $sql = "UPDATE " . static::$table . " SET ";
            foreach ($attributes as $key => $value) {
                $sql .= "{$key} = '{$value}'";
                if($key !== array_key_last($attributes))
                    $sql .= ", ";
            }
            if($this->timestamps) {
                $sql .= ", updated_at = '" . date('Y-m-d H:i:s') . "'";
            }
            $sql .= " WHERE " . static::$primary_key . " = '{$this->attributes[static::$primary_key]}';";
            try {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
                return $stmt->rowCount() ? true : false;
            } catch (\PDOException $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }

    public function presave() {
        if($this->exists) {
            foreach ($this->attributes as $key => $value) {
                $data[$key] = $value;
            }
            $this->touch();
            return $this->update($data) ? true : false;
        } else {
            foreach ($this->attributes as $key => $value) {
                $data[$key] = $value;
            }
            $this->touch();
        static::create($data);
        }
        return true;
    }

    public function prepush() {
        // TODO : push data to database
    }

    public function prefill(array $data) {
        // TODO : fill data to model
    }


    // Model querying

    public static function preall($col = ['*'])
    {
        static::isSetTable();
         $columns = $col[0] === '*' ? $col[0] : static::$primary_key . ",". implode(',', $col);
        $sql = "SELECT " . $columns . " FROM " . static::$table;
        $stmt = (new static)->pdo->prepare($sql);
        $stmt->execute();
        $models = [];
        foreach($stmt->fetchAll(\PDO::FETCH_ASSOC) as $data) {
            $models[] = (new static)->createModel($data);
        }
        return $models;
    }

    public static function prefind($id, $col = ['*'])
    {
        static::isSetTable();
        $columns = $col[0] === '*' ? $col[0] : static::$primary_key . ",". implode(',', $col);
        $sql = "SELECT ".$columns . " FROM " . static::$table . " WHERE " . static::$primary_key . " = {$id}";
        $stmt = (new static)->pdo->prepare($sql);
        $stmt->execute();
        $data =  (new static)->createModel($stmt->fetchAll(\PDO::FETCH_ASSOC)[0]?? NULL);
        return $data;
    }

    public static function prefindOrFail($id, $col = ['*'])
    {
        static::isSetTable();
        $data = static::find($id, $col);
        if(!$data->attributes) throw 
            (new ModelNotFoundExeption("Model not found"))
            ->setModel(
                static::$table, $id
            );
        return $data;
    }

    public static function prefirstOrCreate(array $attributes, array $values = [])
    {
        static::isSetTable();
        $model = static::where($attributes)->first();
        if(!$model) {
            $model = (new static)->createModel([...$attributes, ...$values]);
            $model->save();
        }
        return $model;
    }

    public static function prefirstOrNew(array $attributes, array $values = [])
    {
        static::isSetTable();
        $model = static::where($attributes)->first();
        if(!$model) {
            $model = (new static)->createModel([...$attributes, ...$values]);
        }
        return $model;
    }

    public function prewhere($column, $operator = '=', $value = null, $boolean = 'and')
    {
        static::createInstance();
        static::isSetTable();
        static::$instance->builder->where($column, $operator, $value, $boolean);
        return static::$instance;
    }

    public function preorWhere($column, $operator = '=', $value = null)
    {
        $this->builder->orWhere($column, $operator, $value);
        return $this;
    }

    public function preorderBy($column, $direction = 'asc')
    {
        $this->builder->orderBy($column, $direction);
        return $this;
    }

    public function preget($columns = ['*'])
    {
        static::isSetTable();
        if(!in_array($columns, [static::$primary_key]) && $columns[0] !== '*') {
            $columns = [...$columns,static::$primary_key];
        }
        $data = static::$instance->builder->select(...$columns)->get();
        $models = [];
        foreach($data as $value) {
            $models[] = (new static)->createModel($value);
        }
        return $models;
    }

    public static function prefirst($columns = ['*'])
    {
        static::createInstance();
        if(!in_array($columns, [static::$primary_key]) && $columns[0] !== '*') {
            $columns = [...$columns,static::$primary_key];
        }
        $data = static::$instance->builder->select(...$columns)->first();
        return (new static)->createModel($data);
    }

    // Model deletion

    public static function predestroy($id)
    {
        $sql = "DELETE FROM " . static::$table . " WHERE " . static::$primary_key . " = {$id}";
        $stmt = (new static)->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() ? true : false;
    }

    public function predelete()
    {
        if (static::$instance !== null) {
            return $this->builder->delete();
        }
        if (!isset($this->attributes[static::$primary_key])) {
            return false;
        }
        $sql = "DELETE FROM " . static::$table . " WHERE " . static::$primary_key . " = {$this->attributes[static::$primary_key]}";
        $stmt = (new static)->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() ? true : false;
    }

    // Model relationships

    public function pregetKeyName()
    {
        return $this->primary_key;
    }

    public function __debugInfo()
    {
        $vars = get_object_vars($this);
        unset($vars['pdo'],$vars['query']);
        return $vars;
    }

    public function jsonSerialize()
    {
        return $this->attributes;
    }

    private static function createInstance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }
    }

    private function createModel($data) {
        if(!$data) return null;
        $data = array_diff_key($data, array_flip($this->hidden));
        $model = new static();
        $model->exists = true;
        $model->attributes = $data;
        return $model;
        
    }

    private static function isSetTable() {
        if(!isset(static::$table)) {
            throw new \RuntimeException("Table name not set");
        }
    }
}
