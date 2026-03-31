<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    protected $fillable = [
        'img',
        'place',
        'caption',
        'post_as',
        'link_to_journal',
        'user_id',
    ];

    protected $casts = [
        'link_to_journal' => 'boolean',
    ];

    // 🔒 Hide original image column
    protected $hidden = [
        'img',
    ];

    // ➕ Append accessor automatically
    protected $appends = [
        'file_url',
    ];

    // 📸 Accessor for full image URL
    public function getFileUrlAttribute()
    {
        if ($this->img) {
            return asset('storage/' . $this->img);
        }

        return null;
    }
}