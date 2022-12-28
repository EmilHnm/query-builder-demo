<?php
namespace Hoangm\Query;

use Hoangm\Query\Model\Model;

class User extends Model
{
    protected static string $table = 'users';
    protected array $hidden = ['password'];
}