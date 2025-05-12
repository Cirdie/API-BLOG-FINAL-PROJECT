<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Post;

class SavedPost extends Model
{
    protected $primaryKey = 'Saved_Id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['User_Id', 'Post_Id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'User_Id', 'user_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'Post_Id', 'Post_Id');
    }
}
