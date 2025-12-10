<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Danh sách khách hàng (từ users)
     */
    public function index(Request $r)
    {
        $q = trim((string) $r->q);
        // Lấy user có role "customer" + số đơn hàng + tổng tiền
        $customers = User::query()
    ->withCount('orders')                     
    ->doesntHave('roles')
    ->when($q, function ($qr) use ($q) {
        $qr->where(function ($x) use ($q) {
            $x->where('name', 'like', "%{$q}%")
              ->orWhere('email', 'like', "%{$q}%")
              ->orWhere('phone', 'like', "%{$q}%");
        });
    })
    ->latest('created_at')
    ->paginate(10)
    ->withQueryString();

        return view('dashboard.customers.index', compact('customers', 'q'));
    }


    /**
     * Tạo khách hàng
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone'    => 'nullable|string|max:11',
        ]);

        // 1. Tạo user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'phone'    => $request->phone,
            'avatar'   => 'default.png'
        ]);

        // 2. Gán role customer
        $roleId = Role::where('name', 'customer')->value('id');
        if ($roleId) {
            $user->roles()->attach($roleId);
        }

        return redirect()->route('admin.customers.index')
            ->with('success', 'Thêm khách hàng thành công');
    }


    /**
     * Form sửa khách hàng
     */
    public function edit(User $user)
    {
        // Load đơn hàng để xem thông tin
        $user->load(['orders' => function ($q) {
            $q->select('order_id', 'user_id', 'status', 'total', 'created_at');
        }]);

        return view('dashboard.customers.edit', compact('user'));
    }


    /**
     * Cập nhật khách hàng
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            'phone' => 'nullable|string|max:11',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Cập nhật khách hàng thành công!');
    }


    /**
     * Xóa khách hàng
     */
    public function destroy(User $user)
    {
        // Option: xóa luôn đơn hàng của user
        // $user->orders()->delete();

        $user->delete();
        return redirect()->route('admin.customers.index')
            ->with('success', 'Xóa khách hàng thành công');
    }
}
