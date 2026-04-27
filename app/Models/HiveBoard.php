<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HiveBoard extends Model
{
    protected $fillable = [
        'title',
        'place',
        'lng',
        'lat',
        'tags',
        'desc',
        'post_as',
        'file',
        'user_id',
        'event_date',
        'link_to_journal',
    ];

    protected $casts = [
        'tags' => 'array',
        'link_to_journal' => 'boolean',
    ];

    // Hide raw file path
    protected $hidden = ['file'];

    // Add file_url in response
    protected $appends = ['file_url'];

    // Accessor for file_url using asset()
    public function getFileUrlAttribute()
    {
        return $this->file
            ? asset('storage/' . $this->file)
            : null;
    }
}
