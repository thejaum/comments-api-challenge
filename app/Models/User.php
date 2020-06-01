<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class User extends Model
{
    public $table = "users";
    protected $primaryKey = 'id_user';
    protected $fillable = [
        'id_user',
        'name',
        'username',
        'sign',
        'subscribe',
        'birthdate',
        'email',
        'coin_balance'
    ];
}
?>