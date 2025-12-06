<?php
namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem;
use App\Services\CustomerSyncService;

class ordersService
{
    public function createOrder($user, $cart, $orderCode, $request)
    {
        try {
            $order = DB::transaction(function () use ($user, $cart, $orderCode, $request) {
                $order = $user->orders()->create([
                    'order_code'      => $orderCode,
                    'total_amount'    => 0,                 // cập nhật sau
                    'payment_method'  => $request->provider, // 'vnpay' | 'momo'|'cod'
                    'payment_status'  => $request->provider=='vnpay'||$request->provider=='momo'?'paid':'pending',
                    'status'          => 'pending',
                    'shipping_address'=> $request->input('address'),
                    'phone'           => $request->input('phone'),
                    'note'            => $request->input('note'),
                ]);

                $total = 0;
                foreach ($cart->items as $ci) {
                    $price = (int) product_final_price($ci->product);
                    $qty   = (int) $ci->quantity;

                    OrderItem::create([
                        'order_id'        => $order->order_id,
                        'product_id'      => $ci->product_id,
                        'quantity'        => $qty,
                        'price'           => $price,
                        'discount_percent'=> 0,
                        'discount_price'  => 0,
                    ]);

                    $total += $price * $qty;
                }

                $order->update(['total_amount' => $total]);
                return $order;
            });
            CustomerSyncService::Update_Address_User($order, false);
            return true;
        } catch (Exception $e) {
            dd($e->getMessage());
            return false;
        }

    }
    }
