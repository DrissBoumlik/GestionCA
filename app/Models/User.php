<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname', 'email', 'password', 'picture',
        'role_id', 'status', 'gender', 'agence_name', 'agent_name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin()
    {
        return $this->role->name == 'admin';
    }

    public function isAgency()
    {
        return $this->role->name == 'agence';
    }

    public function isAgent()
    {
        return $this->role->name == 'agent';
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function filters()
    {
        return $this->hasMany(Filter::class);
    }
}
