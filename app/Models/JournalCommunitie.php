<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalCommunitie extends Model
{
    protected $table = 'journal_communities';
    protected $fillable = ['user_id', 'communities_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function community()
    {
        return $this->belongsTo(Community::class, 'communities_id');
    }
}
