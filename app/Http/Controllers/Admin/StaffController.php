<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StaffController extends Controller
{
    /** ================================
     * HIỂN THỊ DANH SÁCH NHÂN VIÊN
     * ================================= */
    public function index()
    {
     $staffs = User::whereHas('roles', function ($q) {
    $q->where('role_user.role_id', 2);
})->paginate(10);

        return view('dashboard.staffs.index', compact('staffs'));
    }

    /** ================================
     * TRANG TẠO NHÂN VIÊN
     * ================================= */
    public function create()
    {
        return view('dashboard.staffs.create');
    }


    /** ================================
     * LƯU NHÂN VIÊN MỚI
     * ================================= */
    public function store(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'name'      => 'required|string|max:255',
                'email'     => 'required|email|unique:users,email',
                'password'  => 'required|min:6|confirmed',
                'phone'     => 'nullable|string|max:20',
                'address'   => 'nullable|string|max:255',
                'avatar'    => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            ]);

            DB::beginTransaction();

            /** 1. TẠO USER */
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'phone'    => $request->phone,
                'address'  => $request->address,
                'password' => Hash::make($request->password),
            ]);

            if (!$user) {
                throw new \Exception("Không thể tạo user.");
            }

            /** 2. GÁN ROLE NHÂN VIÊN */
            $user->roles()->attach(2);  // role_id = 2 (staff)

            /** 3. UPLOAD AVATAR */
            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $path;
                $user->save();
            }

            DB::commit();

            return redirect()->route('admin.staffs.index')
                ->with('success', 'Tạo nhân viên thành công!');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error("Lỗi tạo nhân viên: " . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Không thể tạo nhân viên: ' . $e->getMessage());
        }
    }


    /** ================================
     * TRANG CHỈNH SỬA NHÂN VIÊN
     * ================================= */
    public function edit($id)
    {
        $staff = User::findOrFail($id);
        return view('dashboard.staffs.edit', compact('staff'));
    }


    /** ================================
     * CẬP NHẬT NHÂN VIÊN
     * ================================= */
    public function update(Request $request, $id)
    {
        $staff = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($staff->user_id, 'user_id')],
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
            'password' => 'nullable|min:6|confirmed',
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $staff->name    = $request->name;
            $staff->email   = $request->email;
            $staff->phone   = $request->phone;
            $staff->address = $request->address;

            if ($request->filled('password')) {
                $staff->password = Hash::make($request->password);
            }

            /** Avatar */
            if ($request->hasFile('avatar')) {
                if ($staff->avatar) {
                    Storage::disk('public')->delete($staff->avatar);
                }
                $staff->avatar = $request->file('avatar')->store('avatars', 'public');
            }

            $staff->save();

            DB::commit();

            return redirect()->route('admin.staffs.index')->with('success', 'Cập nhật nhân viên thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }


    /** ================================
     * XÓA NHÂN VIÊN
     * ================================= */
    public function destroy($id)
    {
        $staff = User::findOrFail($id);

        DB::beginTransaction();

        try {

            // Xóa avatar
            if ($staff->avatar) {
                Storage::disk('public')->delete($staff->avatar);
            }

            // Xóa roles
            $staff->roles()->detach();

            // Xóa user
            $staff->delete();

            DB::commit();

            return back()->with('success', 'Xóa nhân viên thành công!');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Lỗi khi xóa nhân viên!');
        }
    }
}
