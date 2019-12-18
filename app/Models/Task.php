<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /**
     * The attributes that are mass $guarded.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
