<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $primaryKey = 'Topic_Id';

    protected $fillable = ['name'];

    // ✅ Hide timestamps in JSON responses
    protected $hidden = ['created_at', 'updated_at'];

    public function posts()
    {
        return $this->hasMany(Post::class, 'Topic_Id', 'Topic_Id');
    }
}
