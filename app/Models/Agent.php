<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pseudo',
        'fullName',
        'hours',
        'imported_at',
        'imported_at_annee',
        'imported_at_mois',
        'imported_at_semaine',
        'isNotReady'
    ];
}
