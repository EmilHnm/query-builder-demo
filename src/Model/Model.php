<?php

namespace Hoangm\Query\Model;

use Hoangm\Query\Builder\QueryBuilder;

class Model extends QueryBuilder
{

    public static function find($id)
    {
        return (new QueryBuilder(static::$table))->where(static::$primary_key, '=', $id)->first();
    }

    public static function create($data)
    {
        return (new QueryBuilder(static::$table))->insert($data);
    }

    public static function all()
    {
        return (new self(static::$table))->get();
    }
}
