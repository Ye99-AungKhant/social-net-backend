<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    use HasFactory;

    protected $fillable = ['adding_user_id', 'added_user_id', 'status'];

    // User who added this friend
    public function addingUser()
    {
        return $this->belongsTo(User::class, 'adding_user_id');
    }

    // User who was added as a friend
    public function addedUser()
    {
        return $this->belongsTo(User::class, 'added_user_id');
    }
}
