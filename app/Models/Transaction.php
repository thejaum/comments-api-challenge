<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Transaction extends Model
{
    public $table = "transactions";
    protected $fillable = [
        'id_transaction',
        'id_highlight_comment',
        'coin_amount',
        'type'
    ];
}
?>