<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HighlightComment extends Model
{
    public $table = "highlight_comment";
    protected $fillable = [
        'id_highlight_comment',
        'id_comment',
        'expiration_date'
    ];
}
?>