<?php

// include './src/DB.php';
// include './src/QueryBuilder.php';
include './src/Users.php';
include './src/DB.php';
// $result = QueryBuilder::table('users')::all();

$user = DB::table('users')->get();

$result = Users::find(2016020001);
var_dump($user);
