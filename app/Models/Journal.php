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
        'file',
        'video',
        'link_to_buzz',
        'user_id',
        'buzz_id',
        'lng',
        'lat'
    ];

    protected $hidden = ['file', 'video'];

    protected $appends = ['file_url', 'video_url'];

    protected $casts = [
        'category'   => 'integer',
        'rating'     => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getFileUrlAttribute(): ?string
    {
        return $this->file ? asset('storage/' . $this->file) : null;
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->video ? asset('storage/' . $this->video) : null;
    }

    public function users()
    {
        return $this->belongs(journal::class);
    }

        public function buzz()
    {
        return $this->belongsTo(Buzz::class);
    }
}
