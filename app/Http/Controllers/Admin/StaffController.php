<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = Staff::with('user')->paginate(10);  // Load relation để tránh null
        return view('dashboard.staffs.index', compact('staffs'));
    }

    public function create()
    {
        return view('dashboard.staffs.create');  // Bỏ $staffs param không cần
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',  // Unique trên users.email
            'phone'    => 'required|max:15',
            'address'  => 'nullable|string',
            'password' => 'required|min:6|confirmed',  // Thêm confirmed nếu có password_confirmation
        ]); 
        DB::beginTransaction();
        try {
            // 1. Tạo user đăng nhập
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // 2. Gán quyền STAFF (giả sử role_id=2)
            $user->roles()->attach(2);

            // 3. Tạo hồ sơ staff
            Staff::create([
                'user_id'    => $user->user_id,  // Giả sử User PK là id; nếu user_id thì adjust
                'full_name'  => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'address'    => $request->address,
                'position'   => 'Nhân viên',  // Default hoặc từ request
                'hire_date'  => now(),
                'status'     => 1,
            ]);

            DB::commit();
            return redirect()->route('admin.staffs.index')
                ->with('success', 'Tạo nhân viên thành công');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $staff = Staff::with('user')->findOrFail($id);  // Load user để tránh null
        return view('dashboard.staffs.edit', compact('staff'));
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        $user = $staff->user;  // Load user

      

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->user_id, 'userid'),  // Ignore PK của user (giả sử id)
            ],
            'phone'   => 'required|string|max:15',
            'address' => 'nullable|string',
            'salary'  => 'nullable|numeric|min:0',
        ]);

        // Cập nhật user
        $user->update([
            'name'  => $request->name,
            'email' => $request->email
        ]);

        // Cập nhật staff
        $staff->update([
            'full_name' => $request->name,  // Sync với user name
            'email'     => $request->email,
            'phone'     => $request->phone,
            'address'   => $request->address,
            'salary'    => $request->salary,
        ]);

        return redirect()->route('admin.staffs.index')->with('success', 'Cập nhật thành công');
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        $user = $staff->user;

        if ($user) {
            $user->delete();  // Xóa user trước
        }

        $staff->delete();

        return redirect()->route('admin.staffs.index')->with('success', 'Xóa nhân viên thành công');
    }
}