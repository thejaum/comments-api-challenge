<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $table = "comments";
    protected $primaryKey = 'id_comment';
    protected $fillable = [
        'id_comment',
        'id_post',
        'id_user',
        'message',
        'created_at',
        'updated_at'
    ];

    public function getIdComment() {
        return $this->username;
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