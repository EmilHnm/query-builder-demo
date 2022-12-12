<?php

namespace Hoang\Query\Model;

use Hoang\Query\Builder\QueryBuilder;

class Model extends QueryBuilder
{

    public static function find($id)
    {
        return (new QueryBuilder(static::$table))->where(static::$primary_key, '=', $id)->first();
    }

    public static function findOne($id)
    {
        return (new QueryBuilder(static::$table))->where(static::$primary_key, '=', $id)->first();
    }

    public static function create($data)
    {
        return (new QueryBuilder(static::$table))->insert($data);
    }
}
