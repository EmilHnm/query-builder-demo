<?php

require 'vendor/autoload.php';

use Hoangm\Query\DB;
use Hoangm\Query\User;
// $user = DB::table('users')->get();
// dd($user);
$data = User::find(11);
$data->email = 'test@example.com';
$data->save();
dd($data);
