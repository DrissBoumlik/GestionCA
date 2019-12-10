<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables as YDT;
use Yajra\DataTables\Facades\DataTables;

class SkillController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $topSkills = $user->topSkills()->map(function ($skill) {
            $skill['isTopSkill'] = true;
            return $skill;
        });
        $nonTopSkills = $user->nonTopSkills()->map(function ($skill) {
            $skill['isTopSkill'] = false;
            return $skill;
        });
        $skills = $topSkills->merge($nonTopSkills);
        return view('skills.index')->with(['skills' => $skills]);
//        return view('skills.index')->with(['topSkills' => $topSkills, 'nonTopSkills' => $nonTopSkills]);
    }

    public function store(Request $request)
    {
        if ($request->name) {
            $skill = Skill::create([
                'name' => $request->name
            ]);
            if ($skill) {
                return response()->json(['message' => 'Created Successfully'], 200);
            }
            return response()->json(['message' => 'Something went wrong'], 422);
        }
        return response()->json(['message' => 'Empty Data'], 401);
    }

    public function update(Request $request, Skill $skill)
    {
        $skill->name = $request->name;
        $updated = $skill->update();
        if ($updated) {
            return response()->json(['message' => 'Updated Successfully'], 200);
        }
        return response()->json(['message' => 'Something went wrong'], 422);
    }

    public function getUserSkills(Request $request)
    {
        $user = auth()->user();
        $skills = $user->skills;
        if ($request->search) {
            $skills = $skills->filter(function ($skill) use ($request) {
                return Str::contains(strtolower($skill->name), strtolower($request->search));
            })->values();
            $skills = collect($skills);
            return ['skills' => $skills];
        }
        $skills = $skills->map(function ($skill) {
            $skill['isTopSkill'] = $skill->pivot->isTopSkill;
            return $skill;
        });
        return ['skills' => $skills];
    }

    public function getSkills(Request $request, YDT $dataTables)
    {
        $user = auth()->user();
        $skills = $user->skills;
        $allSkills = Skill::all();
        $NonAssignedSkills = $allSkills->diff($skills);
        $NonAssignedSkills = $NonAssignedSkills->map(function ($skill) {
            $skill['assigned'] = false;
            return $skill;
        });
        $skills = $skills->map(function ($skill) {
            $skill['assigned'] = true;
            $skill['isTopSkill'] = $skill->pivot->isTopSkill;
            return $skill;
        });
        $allSkills = $skills->merge($NonAssignedSkills);
        if ($request->searchTerm) {
            $allSkills = $allSkills->filter(function ($skill) use ($request) {
                return Str::contains(strtolower($skill->name), strtolower($request->searchTerm));
            })->values();
            return ['skills' => $allSkills];
        }
//        usort($NonAssignedSkills, function ($s1, $s2) {
//            return $s1->assigned && !$s2->assigned ? -1
//                : (!$s1->assigned && $s2->assigned ? 1 : 0);
//        });

        return !$request->exists('dt') ?
            ['skills' => $allSkills] :
            DataTables::of($allSkills)
                ->setRowId(function ($skill) {
                    return 'skill-' . $skill->id;
                })
                ->toJson();
    }

    public function chooseSkill(Request $request)
    {
        $validation = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'skill_id' => ['required', 'exists:skills,id']
        ]);

        if ($request->status == 'false') {
            $skill = UserSkill::where(['user_id' => $request->user_id, 'skill_id' => $request->skill_id])->first();
            $deleted = $skill->forceDelete();
            if ($deleted) {
                return response()->json(['message' => 'Deleted Successfully'], 200);
            }
            return response()->json(['message' => 'Something went wrong'], 422);
        }

        $skill = UserSkill::create([
            'user_id' => $request->user_id,
            'skill_id' => $request->skill_id
        ]);

        if ($skill) {
            return response()->json(['message' => 'Assigned Successfully'], 200);
        }
        return response()->json(['message' => 'Something went wrong'], 422);
    }

    public function chooseTopSkill(Request $request)
    {
        $MAX_TOP_SKILLS = config('custom_params.MAX_TOP_SKILLS');
        if ($request->topSkills && count($request->topSkills) > $MAX_TOP_SKILLS) {
            return back()->withErrors(['message' => 'You can\'t choose more than ' . $MAX_TOP_SKILLS]);
        }
        $user = auth()->user();
        \DB::table('user_skills')
            ->where('user_id', $user->id)
            ->update(['isTopSkill' => false]);
        if ($request->topSkills) {
            \DB::table('user_skills')
                ->where('user_id', $user->id)
                ->whereIn('skill_id', $request->topSkills)
                ->update(['isTopSkill' => true]);
        }
        return back()->with(['message' => 'Updated Successfully']);
    }

    public function editSkills()
    {
        return view('skills.edit');
    }

    public function updateSkills(Request $request)
    {
        $MAX_TOP_SKILLS = config('custom_params.MAX_TOP_SKILLS');
        if ($request->topSkills && count($request->topSkills) > $MAX_TOP_SKILLS) {
            return response()->json(['message' => 'You can\'t choose more than'], 422);
//            return back()->withErrors(['message' => 'You can\'t choose more than ' . $MAX_TOP_SKILLS]);
        }
        $user = auth()->user();

        // Delete all user skills
        UserSkill::removeAll($user);

        // Remove duplicates skills <=> top skills
        $skills = collect($request->skills);
        $topSkills = collect($request->topSkills);
        $skills = $skills->diff($topSkills);

        $_skills = [];
        $skills->map(function ($skill) use ($user, &$_skills) {
            $_skills[] = ['skill_id' => $skill, 'user_id' => $user->id, 'isTopSkill' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        });
        $_topSkills = [];
        $topSkills->map(function ($skill) use ($user, &$_topSkills) {
            $_topSkills[] = ['skill_id' => $skill, 'user_id' => $user->id, 'isTopSkill' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        });
        UserSkill::insert($_skills);
        UserSkill::insert($_topSkills);
        return response()->json(['message' => 'Updated Successfully'], 200);
//        return back()->with(['message' => 'Updated Successfully']);
    }

    public function destroy(Skill $skill)
    {
        // Authorization
        $this->authorize('delete', auth()->user());

        $deleted = $skill->delete();
        if ($deleted) {
            return response()->json(['message' => 'Skill Deleted Successfully'], 200);
        }
        return response()->json(['message' => 'Something went wrong'], 422);
    }
}
