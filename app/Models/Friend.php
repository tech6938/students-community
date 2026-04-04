<?php

namespace App\Models;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'accepted',
    ];
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }
}
