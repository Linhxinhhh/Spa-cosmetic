<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StaffAccountController extends Controller
{
    public function index()
    {
        $staff = Staff::with('user')->get();
        return view('admin.staffs.index', compact('staff'));
    }

    public function create()
    {
        return view('dashboard.staffs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|max:15',
            'address'  => 'nullable|string',
            'salary'   => 'nullable|numeric',
            'password' => 'required|min:6'
        ]);

        DB::beginTransaction();
        try {
            // 1. Tạo user đăng nhập
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // 2. Gán quyền STAFF
            $user->roles()->attach(2); // 2 = staff (tùy DB của bạn)

            // 3. Tạo hồ sơ nhân viên
            Staff::create([
                'user_id' => $user->id,
                'phone'   => $request->phone,
                'address' => $request->address,
                'salary'  => $request->salary,
            ]);

            DB::commit();
            return redirect()->route('staff.index')->with('success', 'Tạo nhân viên thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $staff = Staff::with('user')->findOrFail($id);
        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        $request->validate([
            'name'    => 'required',
            'phone'   => 'required',
            'address' => 'nullable',
            'salary'  => 'nullable|numeric',
            'email'   => 'required|email|unique:users,email,' . $staff->user_id,
        ]);

        // Cập nhật user
        $user = $staff->user;
        $user->update([
            'name'  => $request->name,
            'email' => $request->email
        ]);

        // Cập nhật staff
        $staff->update([
            'phone'   => $request->phone,
            'address' => $request->address,
            'salary'  => $request->salary,
        ]);

        return redirect()->route('staff.index')->with('success', 'Cập nhật thành công');
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);

        // Xóa nhân viên = xóa user luôn
        $user = $staff->user;

        $staff->delete();
        $user->delete();

        return redirect()->route('staff.index')->with('success', 'Xóa nhân viên thành công');
    }
}
