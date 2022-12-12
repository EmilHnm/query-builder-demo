<?php

require 'vendor/autoload.php';

use Hoangm\Query\DB;

$user = DB::table('users')->get();
