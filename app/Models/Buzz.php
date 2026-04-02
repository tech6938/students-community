<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buzz extends Model
{

protected $table = 'buzzes';

    protected $fillable = [
        'user_id',
        'location',
        'place',
        'buzz_type',
        'tags',
        'beelo_mission',
        'rating',
        'img',
        'desc',
    ];

    protected $casts = [
        'tags' => 'array',
        'beelo_mission' => 'boolean',
        'rating' => 'float',
    ];




    // Relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
