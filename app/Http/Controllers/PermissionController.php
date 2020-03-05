<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables as YDT;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function index()
    {
        // Authorization
        $this->authorize('view', auth()->user());

        return view('permissions.index');
    }

    public function getPermissions(Request $request, YDT $dataTables)
    {
        $permissions = Permission::all();
        return DataTables::of($permissions)
            ->setRowId(function ($permission) {
                return 'permission-' . $permission->id;
            })
            ->toJson();
    }

    public function getRoles(Request $request, Permission $permission)
    {
        $roles = $permission->roles;
        $allRoles = Role::all();
        $assignedRoles = [];
        foreach ($allRoles as $role) {
            $assignedRoles[] = $role;
            $role ['assigned'] = assignedPermission($roles, $role);
        }
        return DataTables::of($assignedRoles)
            ->setRowId(function ($role) {
                return 'role-' . $role->id;
            })
            ->toJson();
    }

    public function create()
    {
        // Authorization
        $this->authorize('create', auth()->user());

        return view('permissions.create');
    }

    public function store(Request $request)
    {
        // Authorization
        $this->authorize('create', auth()->user());
        // Valilidation
        $validation = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'method' => ['required', 'string', 'max:255'],
        ]);
        // store
        $permission = Permission::create([
            'name' => $request->name,
            'method' => $request->get('method'),
        ]);

        return redirect('/permissions');
    }

    public function show(Permission $permission)
    {
        // Authorization
        $this->authorize('view', auth()->user());

        $roles = $permission->roles;
        return view('permissions.show')->with(['permission' => $permission, 'roles' => $roles]);
    }

    public function update(Request $request, Permission $permission)
    {
        // Authorization
        $this->authorize('update', auth()->user());

        // Validation
        $validation = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'method' => ['required', 'string', 'max:255'],
        ]);
        $permission->name = $request->name;
        $permission->method = $request->get('method');
        $permission->update();

        return back()->with('message', 'Successfully updated');
    }

    public function destroy(Permission $permission)
    {
        // Authorization
        $this->authorize('delete', auth()->user());

        $deleted = $permission->delete();
        if ($deleted) {
            return response()->json(['message' => 'Permission Deleted Successfully'], 200);
        }
        return response()->json(['message' => 'Un problème est survenue'], 422);
    }
}
