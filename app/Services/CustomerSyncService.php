<?php
namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CustomerSyncService
{
    public static function touchFromOrder(Order $order, bool $paid = false): void
    {
        $user = $order->user;
        if (!$user) return;

        $userKey  = $user->getKey();   // = user_id
        $orderKey = $order->getKey();  // = order_id

        $name    = $user->name ?? $order->shipping_name ?? $order->billing_name ?? null;
        $email   = $user->email ?? $order->email ?? null;
        $phone   = $user->phone ?? $order->phone ?? $order->shipping_phone ?? null;
        $address = $order->shipping_address ?? $order->billing_address ?? $user->address ?? null;

        DB::transaction(function () use ($userKey, $orderKey, $order, $name, $email, $phone, $address, $paid) {
            $customer = Customer::firstOrNew(['user_id' => $userKey]);

            $alreadyCounted = $paid
                && $customer->exists
                && $customer->last_order_id === $orderKey
                && $customer->last_status === 'paid';

            $customer->fill([
                'name'          => $name,
                'email'         => $email,
                'phone'         => $phone,
                'address'       => $address,
                'last_order_id' => $orderKey,
                'last_order_at' => now(),
                'last_status'   => $order->payment_status ?? $order->status ?? ($paid ? 'paid' : 'pending'),
            ]);

            if (!$customer->exists) {
                $customer->orders_count = (int) ($customer->orders_count ?? 0);
             
            }

            $customer->save();

            if ($paid && !$alreadyCounted) {
                $amount = (float)($order->grand_total ?? $order->total ?? $order->amount ?? 0);
                $customer->increment('orders_count');
              
                $customer->forceFill([
                    'last_status'   => 'paid',
                    'last_order_id' => $orderKey,
                    'last_order_at' => now(),
                ])->save();
            }
        });
    }
    public static function Update_Address_User(Order $order, bool $paid = false): void
    {
        $user = $order->user;
        if (!$user) return;

        $userKey  = $user->getKey();   // = user_id
        $orderKey = $order->getKey();  // = order_id

        $name    = $user->name ?? $order->shipping_name ?? $order->billing_name ?? null;
        $email   = $user->email ?? $order->email ?? null;
        $phone   = $user->phone ?? $order->phone ?? $order->shipping_phone ?? null;
        $address = $order->shipping_address ?? $order->billing_address ?? $user->address ?? null;

        DB::transaction(function () use ($userKey, $orderKey, $order, $name, $email, $phone, $address, $paid) {

            // 1) Tìm customer theo user_id
            $customer = Customer::where('user_id', $userKey)->first();

            // 2) Nếu không có -> không tạo mới -> dừng
            if (!$customer) return;

            // 3) Kiểm tra xem đơn này đã được tính chưa
            $alreadyCounted = $paid
                && $customer->last_order_id === $orderKey
                && $customer->last_status === 'paid';

            // 4) Cập nhật các trường
            $customer->update([
                'name'          => $name,
                'email'         => $email,
                'phone'         => $phone,
                'address'       => $address,
                'last_order_id' => $orderKey,
                'last_order_at' => now(),
                'last_status'   => $order->payment_status ?? $order->status ?? ($paid ? 'paid' : 'pending'),
            ]);

            // 5) Nếu đã thanh toán thì tăng orders_count
            if ($paid && !$alreadyCounted) {
                $customer->increment('orders_count');

                $customer->forceFill([
                    'last_status'   => 'paid',
                    'last_order_id' => $orderKey,
                    'last_order_at' => now(),
                ])->save();
            }
        });
    }
}
