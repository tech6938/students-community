<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $fillable = [
        'category',
        'title',
        'place',
        'rating',
        'notes',
        'img',
        'link_to_buzz',
        'user_id'
    ];
    //accessor for complete url -> img
}
    