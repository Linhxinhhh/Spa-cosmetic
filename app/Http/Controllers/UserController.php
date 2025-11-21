<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Danh sách user
     */
    public function index()
    {
        $users = User::with(['roles', 'customer'])->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Form chỉnh sửa quyền user
     */
    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Cập nhật quyền user
     */
    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Gán quyền mới cho user
        $user->roles()->sync($request->roles);

        // Nếu có role "customer" thì đảm bảo có record trong bảng customers
        if ($user->roles()->where('name', 'customer')->exists()) {
            if (!$user->customer) {
                $user->customer()->create([
                    'user_id',
                    'avatar' => null,
                    'address' => null,
                    'phone' => null,
                    'birthday' => null,
                    'loyalty_points' => 0,
                ]);
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật quyền và thông tin khách hàng thành công!');
    }

    /**
     * Xóa user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->roles()->detach();
        if ($user->customer) {
            $user->customer()->delete();
        }
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Xóa user thành công!');
    }
}
