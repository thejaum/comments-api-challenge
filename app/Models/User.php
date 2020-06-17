<?php

namespace App\Models;

use Carbon\Carbon;
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
        'coin_balance',
        'created_at',
        'updated_at'
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone('America/Sao_Paulo')
            ->toDateTimeString()
        ;
    }
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone('America/Sao_Paulo')
            ->toDateTimeString()
        ;
    }
}
?>