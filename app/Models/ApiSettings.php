<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ApiSettings extends Model
{
    public $table = "api_settings";

    protected $fillable = [
        'comments_allow_amount',
        'comments_allow_seconds',
        'hours_expire_notification',
        'created_at',
        'updated_at'
    ];

    public function getAllowAmount(){
        return $this->attributes('comments_allow_amount');
    }
    public function getAllowSeconds(){
        return $this->attributes('comments_allow_seconds');
    }

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