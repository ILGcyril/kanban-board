<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    protected $table = 'boards';
    protected $fillable = [
        'name',
        'content',
        'space_id'
    ];

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
