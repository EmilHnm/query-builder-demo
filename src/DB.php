<?php

include_once dirname(__FILE__) . '/Builder/QueryBuilder.php';

use src\Builder\QueryBuilder;

class DB extends QueryBuilder
{
    public function __construct($table)
    {
        parent::__construct($table);
    }
}
