<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectUser extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'project_id'];

    public static function removeAllUsers($project)
    {
        \DB::table('project_users')
            ->where('project_id', $project->id)
            ->delete();
    }
}
