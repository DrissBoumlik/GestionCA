<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skill extends Model
{

    use SoftDeletes;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_skills');
    }

    public function userSkill()
    {
        return $this->hasMany(UserSkill::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_skills');
    }
}
