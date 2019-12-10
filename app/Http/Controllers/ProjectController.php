<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectSkill;
use App\Models\ProjectUser;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables as YDT;
use Yajra\DataTables\Facades\DataTables;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('projects.index');
    }

    public function getProjects(Request $request, YDT $dataTables)
    {
        $allProjects = Project::with('techs')->with('users');
        return !$request->exists('dt') ?
            ['techs' => $allProjects->get()] :
            DataTables::of($allProjects)
                ->setRowId(function ($project) {
                    return 'project-' . $project->id;
                })
                ->toJson();
    }

    public function getTechs(Request $request, Project $project = null)
    {
        $allTechs = Skill::all();
        if ($project) {
            $techs = $project->techs;
            $NonAssignedTechs = ($allTechs->diff($techs))->map(function ($tech) {
                $tech['assigned'] = false;
                return $tech;
            });
            $techs = $techs->map(function ($tech) {
                $tech['assigned'] = true;
                return $tech;
            });
            $allTechs = $techs->merge($NonAssignedTechs);
        } elseif ($request->searchTerm) {
            $allTechs = $allTechs->filter(function ($tech) use ($request) {
                return Str::contains(strtolower($tech->name), strtolower($request->searchTerm));
            })->values();
        }
        return ['techs' => $allTechs];
    }

    public function getCollaborators(Request $request, Project $project = null)
    {
        $allUsers = User::all();
        if ($project) {
            $users = $project->users;
            $NonAssignedUsers = ($allUsers->diff($users))->map(function ($tech) {
                $tech['assigned'] = false;
                return $tech;
            });
            $users = $users->map(function ($user) {
                $user['assigned'] = true;
                return $user;
            });
            $allUsers = $users->merge($NonAssignedUsers);
        } elseif ($request->searchTerm) {
            $allUsers = $allUsers->filter(function ($user) use ($request) {
                return Str::contains(strtolower($user->firstname), strtolower($request->searchTerm)) ||
                    Str::contains(strtolower($user->lastname), strtolower($request->searchTerm));
            })->values();
        }
        return ['collaborators' => $allUsers];
    }

    public function show(Project $project)
    {
        return view('projects.show')->with(['project' => $project]);
    }

    public function store(Request $request)
    {
        if ($request->name) {
            $project = Project::create([
                'name' => $request->name
            ]);
            if ($project) {
                if ($request->exists('techs') && count($request->techs)) {
                    $techs = [];
                    collect($request->techs)->map(function ($tech) use ($project, &$techs) {
                        $techs[] = ['skill_id' => $tech, 'project_id' => $project->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    });
                    ProjectSkill::insert($techs);
                }
                return response()->json(['message' => 'Created Successfully'], 200);
            }
            return response()->json(['message' => 'Something went wrong'], 422);
        }
        return response()->json(['message' => 'Empty Data'], 401);
    }

    public function update(Request $request, Project $project)
    {
        // Remove all projects assignement
        ProjectSkill::removeAllSkills($project);
        if ($request->exists('techs') && count($request->techs)) {
            $collaborators = [];
            collect($request->techs)->map(function ($tech) use ($project, &$collaborators) {
                $collaborators[] = ['skill_id' => $tech, 'project_id' => $project->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            });
            ProjectSkill::insert($collaborators);
        }

        ProjectUser::removeAllUsers($project);
        if ($request->exists('collaborators') && count($request->collaborators)) {
            $collaborators = [];
            collect($request->collaborators)->map(function ($user) use ($project, &$collaborators) {
                $collaborators[] = ['user_id' => $user, 'project_id' => $project->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            });
            ProjectUser::insert($collaborators);
        }

        $project->name = $request->name;
        $updated = $project->update();
        if ($updated) {
//            return back()->with(['message' => 'Updated Successfully']);
            return response()->json(['message' => 'Updated Successfully'], 200);
        }
//        return back()->withErrors(['message' => 'Something went wrong']);
        return response()->json(['message' => 'Something went wrong'], 422);
    }


    public function destroy(Project $project)
    {
        // Authorization
        $this->authorize('delete', auth()->user());

        $deleted = $project->delete();
        if ($deleted) {
            return response()->json(['message' => 'Project Deleted Successfully'], 200);
        }
        return response()->json(['message' => 'Something went wrong'], 422);
    }
}
