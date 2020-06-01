<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Notification extends Model
{
    public $table = "notifications";
    protected $primaryKey = 'id_notification';
    protected $fillable = [
        'id_user',
        'id_notification',
        'title',
        'message',
        'visualized_date'
    ];
}
?>