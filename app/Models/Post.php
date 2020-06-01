<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public $table = "posts";
    protected $fillable = [
        'id_post',
        'type',
        'body_message',
        'id_user',
        'file_index',
        'created_at'
    ];
}
?>