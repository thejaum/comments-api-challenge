<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class Notification extends Model
{
    public $table = "notifications";
    protected $primaryKey = 'id_notifications';
    protected $fillable = [
        'id_user',
        'id_notification',
        'title',
        'message',
        'visualized_date',
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