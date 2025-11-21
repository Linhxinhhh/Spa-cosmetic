<?php

namespace App\Http\Controllers\User;

use App\Models\Product;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ContactController extends Controller
{
    public function index() {
    return view('users.contact.index');
}

public function submit(Request $request)
{
    $data = $request->validate([
        'name'=>'required|string|max:120',
        'phone'=>'required|string|max:20',
        'email'=>'nullable|email:rfc,dns',
        'subject'=>'nullable|string|max:120',
        'message'=>'required|string|min:10',
        'hp'=>'present|size:0', // honeypot
    ]);
    unset($data['hp']);

    // nếu có khách đăng nhập, lấy id khách (tuỳ hệ thống của bạn)
    $customerId = auth()->check() ? optional(auth()->user()->customer)->id : null;

    Contact::create([
        'customer_id' => $customerId,
        'name'        => $data['name'],
        'phone'       => $data['phone'] ?? null,
        'email'       => $data['email'] ?? null,
        'subject'     => $data['subject'] ?? null,
        'message'     => $data['message'],
        'status'      => Contact::STATUS_OPEN,
        
    ]);

    return back()->with('status','Cảm ơn bạn! Chúng tôi đã nhận được liên hệ.');
}


}