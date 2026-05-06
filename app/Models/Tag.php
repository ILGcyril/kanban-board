<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tags';
    protected $fillable = [
        'name',
        'space_id',
        'color'
    ];

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }
}
