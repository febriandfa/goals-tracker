<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;

    protected $table = 'progress';

    protected $fillable = [
        'goal_id',
        'name',
        'value',
    ];

    public function goals() {
        return $this->belongsTo(Goals::class,'goal_id','id');
    }

}
