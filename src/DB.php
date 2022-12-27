<?php

namespace Hoangm\Query;

use Hoangm\Query\Builder\QueryBuilder;

class DB extends QueryBuilder
{
    public function __construct($table)
    {
        parent::__construct($table);
    }
}
