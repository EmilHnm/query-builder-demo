## Create Query Builder is a simple, methods-chaining dependency-free library to create SQL Queries simple. Supports databases which are supported by PDO

Người thực hiện : [Ngô Minh Hòa](https://github.com/EmilRailgun)

## Installation

```bash
    composer require "hoang/query"
```

## Configuration

- Create a config.ini file in root folder with content

```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=database_name
    DB_USERNAME=root
    DB_PASSWORD=
```

and customize it

## Usage examples

1. Use Query Builder

   ```php

       <?php
           require 'vendor/autoload.php';
           use Hoang\Query\DB;
           $user = DB::table('users')->get();

   ```

2. Use Model Builder

   - Create a model class

     ```php
         <?php
             namespace App\Models;
             use Hoang\Query\Model;

             class User extends Model
             {
                 protected static $table = 'users'; // table name
                 protected static $primary_key = 'id'; // primary key
             }
     ```

     - Usage

     ```php
         <?php
             require 'vendor/autoload.php';
             use App\Models\User;
             $user = User::all();
     ```
