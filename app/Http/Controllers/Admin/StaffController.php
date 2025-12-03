<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = Staff::with('user')->paginate(10);
        return view('dashboard.staffs.index', compact('staffs'));
    }

    public function create()
    {
        return view('dashboard.staffs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6|confirmed',
            'phone'     => 'required|string|max:15',
            'address'   => 'nullable|string',
            'position'  => 'nullable|string',
            'salary'    => 'nullable|numeric|min:0',
            'avatar'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // 1. Tạo User (tài khoản đăng nhập)
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Gán vai trò nhân viên (nếu dùng Spatie hoặc tự làm)
            $user->roles()->attach(2); // role_id = 2 là Staff

            // 2. Tạo hồ sơ Staff
            $staff = Staff::create([
                'user_id'    => $user->id,
                'phone'      => $request->phone,
                'address'    => $request->address,
                'position'   => $request->position ?? 'Nhân viên',
                'salary'     => $request->salary ?? 0,
                'hire_date'  => now(),
                'status'     => 1,
            ]);

            // 3. Upload avatar (lưu vào staff)
            if ($request->hasFile('avatar')) {
                $staff->avatar = $request->file('avatar')->store('avatars', 'public');
                $staff->save();
            }

            DB::commit();
            return redirect()->route('admin.staffs.index')->with('success', 'Tạo nhân viên thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Lỗi: '.$e->getMessage());
        }
    }

    public function edit($id)
    {
        $staff = Staff::with('user')->findOrFail($id);
        return view('dashboard.staffs.edit', compact('staff'));
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::with('user')->findOrFail($id);
        $user  = $staff->user;

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users','email')->ignore($user->id)],
            'phone'    => 'required|string|max:15',
            'address'  => 'nullable|string',
            'salary'   => 'nullable|numeric|min:0',
            'position' => 'nullable|string',
            'password' => 'nullable|min:6|confirmed',
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Cập nhật User
            $user->name  = $request->name;
            $user->email = $request->email;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            // Cập nhật Staff
            $staff->phone     = $request->phone;
            $staff->address   = $request->address;
            $staff->salary    = $request->salary;
            $staff->position  = $request->position ?? $staff->position;

            // Avatar
            if ($request->hasFile('avatar')) {
                if ($staff->avatar) {
                    Storage::disk('public')->delete($staff->avatar);
                }
                $staff->avatar = $request->file('avatar')->store('avatars', 'public');
            }

            $staff->save();
            DB::commit();

            return redirect()->route('admin.staffs.index')->with('success', 'Cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: '.$e->getMessage());
        }
    }

    public function destroy($id)
    {
        $staff = Staff::with('user')->findOrFail($id);

        DB::beginTransaction();
        try {
            if ($staff->avatar) {
                Storage::disk('public')->delete($staff->avatar);
            }
            $staff->user?->delete(); // xóa user trước
            $staff->delete();

            DB::commit();
            return back()->with('success', 'Xóa nhân viên thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi xóa!');
        }
    }
}