<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function post(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }

    public function media()
    {
        return $this->hasOne(Media::class, 'user_id', 'id');
    }

    // Users who this user has added as friends
    public function friendsAdded()
    {
        return $this->hasMany(Friendship::class, 'adding_user_id');
    }

    // Users who have added this user as a friend
    public function friendsOf()
    {
        return $this->hasMany(Friendship::class, 'added_user_id');
    }

    public function friendsPosts()
    {
        return Post::whereIn('user_id', function ($query) {
            $query->select('added_user_id')
                ->from('friendships')
                ->where('adding_user_id', $this->id);
        })->orWhereIn('user_id', function ($query) {
            $query->select('adding_user_id')
                ->from('friendships')
                ->where('added_user_id', $this->id);
        });
    }

    public function friendsStory()
    {

        return Story::where('user_id', $this->id)
            ->orWhereIn('user_id', function ($query) {
                $query->select('added_user_id')
                    ->from('friendships')
                    ->where('adding_user_id', $this->id);
            })
            ->orWhereIn('user_id', function ($query) {
                $query->select('adding_user_id')
                    ->from('friendships')
                    ->where('added_user_id', $this->id);
            });
    }

    public function getFriendsList($userId, $status)
    {
        $friendsAdded = User::whereIn('id', function ($query) use ($userId, $status) {
            $query->select('added_user_id')
                ->from('friendships')
                ->where('adding_user_id', $userId)->where('status', $status);
        })->get();

        // Users who have added this user as a friend
        $friendsOf = User::whereIn('id', function ($query) use ($userId, $status) {
            $query->select('adding_user_id')
                ->from('friendships')
                ->where('added_user_id', $userId)->where('status', $status);
        })->get();

        // Combine the two collections and remove duplicates
        $friendsList = $friendsAdded->merge($friendsOf)->unique('id');

        return $friendsList;
    }
}
