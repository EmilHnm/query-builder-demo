<?php

require 'vendor/autoload.php';

use Hoangm\Query\DB;
use Hoangm\Query\User;
// $user = DB::table('users')->get();
// dd($user);
$data = User::where('id', '>', 1)->get();
dd($data);
