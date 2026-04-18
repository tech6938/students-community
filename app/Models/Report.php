<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'community_id',
        'hiveboards_id',
        'stories_id',
        'journal_id',
        'issue',
        'description',
    ];

    protected $hidden = ['updated_at'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function community()
    {
        return $this->belongsTo(Community::class, 'community_id');
    }

    public function hiveboard()
    {
        return $this->belongsTo(HiveBoard::class, 'hiveboards_id');
    }

    public function story()
    {
        return $this->belongsTo(Story::class, 'stories_id');
    }
}
