<?php

require 'vendor/autoload.php';

use Hoang\Query\DB;
use Hoang\Query\Users;

$user = DB::table('users')->get();

$result = Users::find(2016020001);
var_dump($user);
