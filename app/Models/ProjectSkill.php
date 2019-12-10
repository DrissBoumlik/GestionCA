<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectSkill extends Model
{
    use SoftDeletes;

    protected $fillable = ['skill_id', 'project_id'];

    public static function removeAllSkills($project)
    {
        \DB::table('project_skills')
            ->where('project_id', $project->id)
            ->delete();
    }
}
