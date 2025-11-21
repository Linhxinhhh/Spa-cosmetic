<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $user = auth('admin')->user(); // ✅ guard admin
        return view('dashboard.profile.index', compact('user'));
    }

    public function edit(): \Illuminate\View\View
    {
        $user = auth('admin')->user(); // ✅ guard admin
        return view('dashboard.profile.edit', compact('user'));
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = auth('admin')->user();              // ✅ guard admin
        $data = $request->validated();              // name, phone, address, (email nếu gửi), avatar?

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars','public'); // storage/app/public/avatars/xxx.jpg

            // Xoá ảnh cũ nếu là file local
            if ($user->avatar && !Str::startsWith($user->avatar, ['http://','https://','/'])) {
                Storage::disk('public')->delete($user->avatar);
            }

            $data['avatar'] = $path;               // lưu path tương đối vào DB
        }

        $user->fill($data)->save();

        return redirect()->route('admin.profile.index')
            ->with('success', 'Cập nhật hồ sơ thành công!');
    }
}
