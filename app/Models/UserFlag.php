<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFlag extends Model
{
    protected $fillable = ['user_id', 'flags'];

    protected $casts = [
        'flags' => 'array'
    ];
}
