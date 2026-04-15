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

    protected $hidden = [
        'img',
    ];

    protected $appends = [
        'file_url',
    ];

    public function getFileUrlAttribute()
    {
        if ($this->img) {
            return asset('storage/' . $this->img);
        }

        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(
            User::class,                // Related model
            'journal_communities',      // Pivot table name
            'communities_id',           // Foreign key on pivot table
            'user_id'                   // Related key on pivot table
        )->withTimestamps();
    }
}
