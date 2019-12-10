<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'method'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }

    public function assignPermission($role_id)
    {
        $relation = PermissionRole::create([
            'role_id' => $role_id,
            'permission_id' => $this->id
        ]);
        return $relation;
    }
}
