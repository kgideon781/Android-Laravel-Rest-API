<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Like;
use App\User;
use App\Comment;

class Post extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function likes(){
        return $this->hasMany(Like::class);
    }
}
