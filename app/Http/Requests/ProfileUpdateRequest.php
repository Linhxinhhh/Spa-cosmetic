<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Cho phép request này (tuỳ bạn có thể siết chặt hơn)
        return true;
    }

   public function rules(): array
{
    $user = $this->user('admin') ?: $this->user();

    $emailUnique = Rule::unique('users','email');
    if ($user) {
        $emailUnique->ignore($user->getKey(), $user->getKeyName());
        // hoặc: $emailUnique->ignoreModel($user); // Laravel 9+
    }

    return [
        'name'    => ['required','string','max:255'],
        'email'   => ['sometimes','email','max:255', $emailUnique], // ✅
        'phone'   => ['nullable','string','max:20'],
        'address' => ['nullable','string','max:255'],
        'avatar'  => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'], // ✅
    ];
}
}
