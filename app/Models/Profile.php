<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'username',
        'home_school',
        'abroad_school',
        'home_city',
        'current_city',
        'languages',
        'interests',
        
    ];

    // ✅ Cast JSON fields
    protected $casts = [
        'languages' => 'array',
        'interests' => 'array',
    ];

    // ✅ Relationship: Profile belongs to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ✅ Relationship: Profile has many photos
    public function photos()
    {
        return $this->hasMany(PublicPhoto::class);
    }
}
