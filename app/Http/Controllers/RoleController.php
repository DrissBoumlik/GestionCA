<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables as YDT;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Authorization
        $this->authorize('view', auth()->user());

        return view('roles.index');
    }

    public function getRoles(Request $request, YDT $dataTables)
    {
        $roles = Role::withCount('permissions')->withCount('users');
        return DataTables::of($roles)
            ->setRowId(function ($role) {
                return 'role-' . $role->id;
            })
            ->toJson();
    }

    public function getPermissions(Request $request, Role $role)
    {
        $permissions = $role->permissions;
        $allPermissions = Permission::all();
        $assignedPermissions = [];
        foreach ($allPermissions as $permission) {
            $assignedPermissions[] = $permission;
            $permission ['assigned'] = assignedPermission($permissions, $permission);
        }
        return DataTables::of($assignedPermissions)
            ->setRowId(function ($permission) {
                return 'permission-' . $permission->id;
            })
            ->toJson();
    }

    public function create()
    {
        // Authorization
        $this->authorize('create', auth()->user());

        return view('roles.create');
    }

    public function store(Request $request)
    {
        // Authorization
        $this->authorize('create', auth()->user());

        $validation = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect('/roles');
    }

    public function show(Role $role)
    {
        // Authorization
        $this->authorize('view', auth()->user());

        $permissions = $role->permissions;
        return view('roles.show')->with(['role' => $role, 'permissions' => $permissions]);
    }

    public function update(Request $request, Role $role)
    {
        // Authorization
        $this->authorize('update', auth()->user());

        $validation = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
        $role->name = $request->name;
        $role->description = $request->description;
        $role->update();

        return back()->with('message', 'Successfully updated');
    }

    public function assignPermissionRole(Request $request)
    {
        // Authorization
        $this->authorize('create', auth()->user());

        $assign = $request->status == 'true' ? true : false;
        $relation = PermissionRole::where(['permission_id' => $request->permission_id, 'role_id' => $request->role_id])->first();
        if (!$assign && $relation) {
            $deleted = $relation->forceDelete();
            if ($deleted) {
                return response()->json(['message' => 'Detached Successfully'], 200);
            }
            return response()->json(['message' => 'Something went wrong'], 422);
        } else if (!$assign) {
            return response()->json(['message' => 'Already Detached'], 200);
        } else if (!$relation) {
            $role = Role::find($request->role_id);
            $relation = $role->assignPermission($request->permission_id);
            if ($relation) {
                return response()->json(['message' => 'Assigned Successfully'], 200);
            }
            return response()->json(['message' => 'Something went wrong'], 422);
        }
        return response()->json(['message' => 'Already Assigned'], 200);
    }

    public function destroy(Role $role)
    {
        // Authorization
        $this->authorize('delete', auth()->user());

        $deleted = $role->delete();
        if ($deleted) {
            return response()->json(['message' => 'Role Deleted Successfully'], 200);
        }
        return response()->json(['message' => 'Something went wrong'], 422);
    }
}
