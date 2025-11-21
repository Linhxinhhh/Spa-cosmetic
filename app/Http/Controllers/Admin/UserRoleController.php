<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        return view('dashboard.user_roles.index', compact('users'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('dashboard.user_roles.edit', compact('user','roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,role_id'
        ]);

        $user->roles()->sync($request->roles);

        return redirect()->route('admin.user_roles.index')->with('success','Cập nhật quyền thành công.');
    }
}
