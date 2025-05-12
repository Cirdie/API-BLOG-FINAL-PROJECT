<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Feedback extends Model
{
    protected $primaryKey = 'Feedback_Id';

    protected $fillable = ['User_Id', 'message'];

    public function user()
    {
        return $this->belongsTo(User::class, 'User_Id', 'User_Id');
    }
}
