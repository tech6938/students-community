<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuzzRating extends Model
{
    protected $table = 'buzz_ratings';

    protected $fillable = [
        'buzzes_id',
        'user_id',
        'flag',
        'rating',
        'tags',
        'img',
        'desc',
    ];

    protected $casts = [
        'tags' => 'array',
        'rating' => 'float',
    ];

    protected $appends = ['img_url'];

    // Accessor for full image URL
    public function getImgUrlAttribute()
    {
        return $this->img
            ? asset($this->img)
            : null;
    }

    // Relationship
    public function buzz()
    {
        return $this->belongsTo(Buzz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
