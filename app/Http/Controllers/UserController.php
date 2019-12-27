<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class UserController extends Controller
{
    public function index() {
        $users = User::orderBy('created_at', 'DESC')->paginate(10);

        return view('users.index', compact('users', 'permission'));
    }

    public function create() {
        $role = Role::orderBy('name', 'ASC')->get();

        return view('users.create', compact('role'));
    }

    public function store(Request $r)
    {
        $this->validate($r, [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|string|exists:roles,name'
        ]);

        $user = User::firstOrCreate([
            'email' => $r->email
        ], [
            'name' => $r->name,
            'password' => bcrypt($r->password),
            'status' => 1
        ]);

        $user->assignRole($r->role);
        return redirect(route('users.index'))->with(['success' => 'User: <strong>' . $user->name . '</strong> Ditambahkan']);
    }


    public function edit($id) {
        $user = User::findOrFail($id);

        return view('users.edit', compact('user'));
    }

    public function update(Request $r, $id) {
        $this->validate($r, [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::findOrFail($id);
        $password = !empty($r->password) ? bcrypt($r->password) : $user->password;
        $user->update([
            'name' => $r->name,
            'password' => $password
        ]);

        return redirect(route('users.index'))->with(['success' => 'User: <strong>' . $user->name . '</strong> Updated']);
    }

    public function destroy($id) {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with(['success' => 'User: <strong>' . $user->name . '</strong> Deleted']);
    }

    public function rolePermission(Request $r) {
        $role = $r->get('role');

        $permission = null;
        $hasPermission = null;

        $roles = Role::all()->pluck('name');

        if (!empty($role)) {
            $getRole = Role::findByName($role);

            $hasPermission = DB::table('role_has_permissions')
                ->select('permissions.name')
                ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                ->where('role_has_permissions.role_id', $getRole->id)->get()->pluck('name')->all();

            $permissions = Permission::all()->pluck('name');
        }
        return view('users.role_permission', compact('roles', 'permissions', 'hasPermission'));
    }

    public function addPermission(Request $r) {
        $this->validate($r, [
            'name' => 'required|string|unique:permissions'
        ]);
        
        $permission = Permission::firstOrCreate([
            'name' => $r->name
        ]);
        
        return redirect()->back();
    }

    public function setRolePermission(Request $r, $role) {
        $role = Role::findByName($role);
        $role->syncPermissions($r->permission);

        return redirect()->back()->with(['success' => 'Permission to Role Saved!']);
    }

    public function roles(Request $request, $id) {
        $user = User::findOrFail($id);
        $roles = Role::all()->pluck('name');

        return view('users.roles', compact('user', 'roles'));
    }

    public function setRole(Request $r, $id) {
        $this->validate($r, [
            'role' => 'required'
        ]);

        $user = User::findOrFail($id);
        $user->syncRoles($r->role);

        return redirect()->back()->with(['success' => 'Role has set']);
    }
}
