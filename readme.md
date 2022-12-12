## Create Query Builder is a simple, methods-chaining dependency-free library to create SQL Queries simple. Supports databases which are supported by PDO

Người thực hiện: [Ngô Minh Hòa](https://github.com/EmilRailgun)

## Installation

```bash
    composer require "hoangm/query @dev"
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
           use Hoangm\Query\DB;
           $user = DB::table('users')->get();

   ```

2. Use Model Builder

   - Create a model class

     ```php
         <?php
             namespace App\Models;
             use Hoangm\Query\Model;

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

## Methods

### Query Builder

- `table($table_name)`

  - Set table name

- `select($columns)`
  - Select columns
  - Example: `select('id, name')` or `select(['id', 'name'])`
- `where($column, $operator, $value)`
  - Where condition
  - Example: `where('id', '=', 1)` or `where(['id' => 1])`
- `orWhere($column, $operator, $value)`

  - Or where condition
  - Example: `orWhere('id', '=', 1)` or `orWhere(['id', '=', 1], ['name', '=', 'Hoang'])`

- `orderBy($column, $order)`

  - Order by
  - Example: `orderBy('id', 'desc')`

- `limit($limit)`

  - Limit
  - Example: `limit(10)`

- `offset($offset)`

  - Offset
  - Example: `offset(10)`

- `join($table, $first, $operator, $second)`

  - Inner Join
  - Example: `join('users', 'users.id', '=', 'posts.user_id')`

- `leftJoin($table, $first, $operator, $second)`

  - Left Join
  - Example: `leftJoin('users', 'users.id', '=', 'posts.user_id')`

- `having($column, $operator, $value)`

  - Having
  - Example: `having('id', '=', 1)` or `having(['id' => 1])`

- `insert($data)`

  - Insert data
  - Example: `insert(['name' => 'Hoang', 'email' => 'hoangominh01@gmail.com'])`

- `update($data)`

  - Update data
  - Example: `update(['name' => 'Hoang2'])->where('id', '=', 1)`

- `delete()`

  - Delete data
  - Example: `delete()->where('id', '=', 1)`

- `get()`

  - Get data
  - Example: `get()`

- `count()`

  - Count data
  - Example: `count()`

- `first()`

      - Get first data
      - Example: `first()`

### Model Builder

- `all()`

  - Get all data
  - Example: `all()`

- `find($id)`

  - Find data by id
  - Example: `find(1)`

- `create($data)`
  - Create data
  - Example: `create(['name' => 'Hoang', 'email' => 'hoangominh01@gmail.com'])`
