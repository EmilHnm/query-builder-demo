<?php


include_once dirname(__FILE__) . '/Builder/QueryBuilder.php';

use src\Builder\QueryBuilder;

class Users extends QueryBuilder
{
    protected static $table = 'users';
    protected static $primary_key = 'id_no';
}
