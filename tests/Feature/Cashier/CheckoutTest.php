<?php

namespace Tests\Feature\Cashier;

use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_submit_manual_cash_payment(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $category = Category::create([
            'name' => 'Coffee',
            'type' => 'drink',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Espresso',
            'slug' => 'espresso',
            'description' => 'Test product',
            'base_price' => 5000,
            'is_active' => true,
            'is_stock_tracked' => false,
        ]);

        $response = $this->actingAs($cashier)->post(route('cashier.orders.store'), [
            'items' => [
                [
                    'product_id' => $product->id,
                    'variant_id' => null,
                    'product_name' => $product->name,
                    'variant_name' => 'Default',
                    'qty' => 2,
                    'price' => 5000,
                ],
            ],
            'payment_mode' => 'manual',
            'payment_method' => 'cash',
            'cash_received' => 12000,
            'discount' => 0,
        ]);

        $response->assertRedirect();

        $order = Order::first();

        $this->assertNotNull($order);
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('accepted', $order->status);
        $this->assertSame('11100.00', $order->total);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'method' => 'cash',
            'status' => 'paid',
            'amount' => 12000,
            'change_amount' => 900,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'qty' => 2,
            'subtotal' => 10000,
        ]);
    }
}
