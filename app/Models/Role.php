<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    public function assignPermission($permission_id)
    {
        $relation = PermissionRole::create([
            'role_id' => $this->id,
            'permission_id' => $permission_id
        ]);
        return $relation;
    }
}
