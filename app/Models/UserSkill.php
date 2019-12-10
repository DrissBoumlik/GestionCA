<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSkill extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'skill_id', 'isTopSkill'];

    public static function isTopSkill($user, $skill)
    {
        $skill = UserSkill::where([
            'user_id' => $user->id,
            'skill_id' => $skill->id
        ])->first();

        return $skill && $skill->isTopSkill == 1;
    }

    public static function addSkill($skill_id, $user_id, $isTopSkill)
    {
        $skill = self::create([
            'skill_id' => $skill_id,
            'user_id' => $user_id,
            'isTopSkill' => $isTopSkill
        ]);
        return $skill;
    }

    public static function removeAll($user)
    {
        \DB::table('user_skills')
            ->where('user_id', $user->id)
            ->delete();
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function bulkUpdate($skills, $status = false, $user = null)
    {
        $user = $user ?? auth()->user();
        \DB::table('user_skills')
            ->where('user_id', $user->id)
            ->whereIn('skill_id', $skills)
            ->update(['isTopSkill' => $status]);
    }
}
