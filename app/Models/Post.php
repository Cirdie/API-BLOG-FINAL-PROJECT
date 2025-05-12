<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Topic;
use App\Models\SavedPost;

class Post extends Model
{
    protected $primaryKey = 'Post_Id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'User_Id',
        'Topic_Id',
        'title',
        'content',
        'is_approved',
        'save_count',
        'image'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'User_Id', 'user_id');
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'Topic_Id', 'Topic_Id');
    }

   public function savedBy()
{
    return $this->hasMany(SavedPost::class, 'Post_Id', 'Post_Id');
}

}
