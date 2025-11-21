<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'cart_items';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'item_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'added_at'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'added_at' => 'datetime',
        'quantity' => 'integer'
    ];

    /**
     * Relationship with cart
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    /**
     * Relationship with product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Increase quantity
     */
    public function increaseQuantity(int $amount = 1)
    {
        $this->quantity += $amount;
        $this->save();
    }

    /**
     * Decrease quantity
     */
    public function decreaseQuantity(int $amount = 1)
    {
        $this->quantity = max(1, $this->quantity - $amount);
        $this->save();
    }

    /**
     * Calculate item total price
     */
    public function getTotalPriceAttribute()
    {
        return $this->product->price * $this->quantity;
    }
}