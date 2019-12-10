<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermissionRole extends Model
{
    use SoftDeletes;

    protected $fillable = ['role_id', 'permission_id'];

    protected $table = 'permission_role';
}
