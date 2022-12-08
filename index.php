<?php

// include './src/DB.php';
// include './src/QueryBuilder.php';
include './src/Model/Users.php';
// $result = QueryBuilder::table('users')::all();

$result = Users::find(2016020001);
var_dump($result);
