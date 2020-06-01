<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ApiSettings extends Model
{
    public $table = "api_settings";

    protected $fillable = [
        'comments_allow_amount',
        'comments_allow_seconds'
    ];

    public function getAllowAmount(){
        return $this->attributes('comments_allow_amount');
    }
    public function getAllowSeconds(){
        return $this->attributes('comments_allow_seconds');
    }
}
?>