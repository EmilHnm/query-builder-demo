<?php

include './src/DB.php';
include './src/QueryBuilder.php';
$config = parse_ini_file(__DIR__ . '/config.ini');

$db = new DB($config);
$db->connect();

$query = new QueryBuilder($db->getConnection());

$query::table('users');
