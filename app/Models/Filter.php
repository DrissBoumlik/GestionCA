<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Filter extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'route', 'date_filter'];

    protected $casts = [
        'date_filter' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
