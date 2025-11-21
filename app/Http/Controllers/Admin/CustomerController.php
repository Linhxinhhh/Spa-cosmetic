<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
   public function index(Request $r)
{
    $q = trim((string) $r->q);

    $customers = Customer::query()
        ->with(['user:user_id,name,email,phone'])         // chỉ lấy cột cần
        ->select(['id','user_id','address','birthday','loyalty_points','created_at'])
        ->when($q, function ($qr) use ($q) {
            $qr->where(function ($x) use ($q) {
                $x->where('address','like',"%{$q}%")
                  ->orWhereHas('user', function ($u) use ($q) {
                      $u->where('name','like',"%{$q}%")
                        ->orWhere('email','like',"%{$q}%")
                        ->orWhere('phone','like',"%{$q}%");
                  });
            });
        })
        ->latest('created_at')
        ->paginate(10)
        ->withQueryString();

    return view('dashboard.customers.index', compact('customers','q'));
}


    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|min:6',
            'phone'          => 'nullable|string|max:11',
            'address'        => 'nullable|string|max:255',
            'birthday'       => 'nullable|date',
            'loyalty_points' => 'nullable|integer|min:0',
        ]);

        // Tạo user trước
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'phone'    => $request->phone,
            'avatar'   => 'default.png', // hoặc null nếu nullable
        ]);

        // Gán role customer cho user
        $roleId = Role::where('name', 'customer')->first()->id ?? null;
        if ($roleId) {
            $user->roles()->attach($roleId);
        }

        // Tạo customer từ user_id vừa tạo
        Customer::create([
            'user_id'        => $user->user_id, // user_id vì bạn đã set primaryKey là user_id
            'address'        => $request->address,
            'birthday'       => $request->birthday,
            'loyalty_points' => $request->loyalty_points ?? 0,
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Thêm khách hàng thành công');
    }

   public function edit(Customer $customer)
{
    return view('dashboard.customers.edit', compact('customer'));
}

public function update(Request $request, Customer $customer)
{
    $request->validate([
        'name'           => 'required|string|max:255',
        'email'          => 'required|email|unique:users,email,' . $customer->user_id . ',user_id',
        'phone'          => 'nullable|string|max:11',
        'address'        => 'nullable|string|max:255',
        'birthday'       => 'nullable|date',
        'loyalty_points' => 'nullable|integer|min:0',
    ]);

    // Cập nhật bảng users (dùng user_id)
    $customer->user->update([
        'name'  => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
    ]);

    // Cập nhật bảng customers
    $customer->update([
        'address'        => $request->address,
        'birthday'       => $request->birthday,
        'loyalty_points' => $request->loyalty_points ?? 0,
    ]);

    return redirect()->route('admin.customers.index')->with('success', 'Cập nhật khách hàng thành công!');
}



    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('admin.customers.index')->with('success', 'Xóa khách hàng thành công');
    }
}
