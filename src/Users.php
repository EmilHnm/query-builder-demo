<?php

namespace Hoang\Query;

use Hoang\Query\Builder\QueryBuilder;

class Users extends QueryBuilder
{
    protected static $table = 'users';
    protected static $primary_key = 'id_no';
}
