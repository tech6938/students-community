<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['email', 'profile_status'];
    protected $hidden = ['created_at', 'updated_at'];


    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function communities()
    {
        return $this->hasMany(Community::class);
    }

    /**
     * Communities saved/added to journal by this user
     * (through the pivot table JournalCommunitie)
     */
    public function savedCommunities()
    {
        return $this->belongsToMany(
            Community::class,           // Related model
            'journal_communities',      // Pivot table name
            'user_id',                  // Foreign key on pivot table
            'communities_id'            // Related key on pivot table
        )->withTimestamps();
    }
}
