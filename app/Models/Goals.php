<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goals extends Model
{
    use HasFactory;

    protected $table = 'goals';

    protected $guarded = [];

    public function progress()
    {
        return $this->hasMany(Progress::class, 'goal_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
