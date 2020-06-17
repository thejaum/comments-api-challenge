<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class Transaction extends Model
{
    public $table = "transactions";
    protected $fillable = [
        'id_transaction',
        'id_highlight_comment',
        'coin_amount',
        'type',
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