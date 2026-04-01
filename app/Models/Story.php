<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $fillable = [
        'post_type',
        'title',
        'desc',
        'place',
        'tags',
        'img',
        'post_as',
        'link_to_journal',
        'user_id'
    ];

    protected $hidden = ['img'];

    protected $appends = ['img_url'];

    protected $casts = [
        'tags'           => 'array',
        'link_to_journal'=> 'boolean',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    public function getImgUrlAttribute(): ?string
    {
        if (!$this->img) return null;

        return asset('storage/' . $this->img) ?: null;
    }
}
