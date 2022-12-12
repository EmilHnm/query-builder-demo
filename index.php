<?php

require 'vendor/autoload.php';

use Hoang\Query\DB;

$user = DB::table('users')->get();
