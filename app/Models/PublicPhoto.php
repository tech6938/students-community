<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicPhoto extends Model
{
    protected $fillable = [
        'profile_id',
        'image',
    ];

    // ✅ Append image URL automatically
    protected $appends = ['image_url'];

    // ✅ Relationship: Photo belongs to Profile
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    // ✅ Accessor for full URL (like your HiveBoard file_url)
    public function getImageUrlAttribute()
    {
        if (!$this->image) return null;

        return asset('storage/' . str_replace('public/', '', $this->image));
    }
}
