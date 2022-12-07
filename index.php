<?php

// include './src/DB.php';
include './src/QueryBuilder.php';

QueryBuilder::table('users')->where('name', '=', 'John')
    ->where(2, '=', 2)
    ->update(['name' => 'John Doe', 'email' => '123@gmail.com']);
