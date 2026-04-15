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

    protected $hidden = ['img'];

    protected $appends = ['img_url'];

    protected $casts = [
        'category'   => 'integer',
        'rating'     => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getImgUrlAttribute(): ?string
    {
        if (!$this->img) return null;

        return asset('storage/' . $this->img) ?: null;
    }
}
