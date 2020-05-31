<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $table = "comments";
    protected $fillable = [
        'id_comment',
        'id_post',
        'id_user',
        'message'
    ];
}
?>