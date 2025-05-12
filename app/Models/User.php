<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'user_id'; // lowercase and matches your migration
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',     // 'user' or 'admin'
        'gender',
        'image'
    ];


    protected $hidden = [
        'password',
        'remember_token',
        'created_at', 
        'updated_at'
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'User_Id', 'user_id');
    }

 public function savedPosts()
{
    return $this->hasMany(\App\Models\SavedPost::class, 'User_Id', 'user_id');
}


    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'User_Id', 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'User_Id', 'user_id');
    }
}
