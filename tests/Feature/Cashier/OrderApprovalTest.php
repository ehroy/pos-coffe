<?php

namespace Tests\Feature\Cashier;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_view_qr_table_order_detail_and_approve_it(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);

        $table = Table::create([
            'code' => 'T001',
            'name' => 'Table 1',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Coffee',
            'type' => 'drink',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Americano',
            'slug' => 'americano',
            'description' => 'Test product',
            'base_price' => 8000,
            'is_active' => true,
            'is_stock_tracked' => false,
        ]);

        $order = Order::create([
            'order_number' => 'QR-20260506-0001',
            'table_id' => $table->id,
            'customer_name' => 'Dani',
            'source' => 'qr_table',
            'status' => 'pending',
            'subtotal' => 16000,
            'discount' => 0,
            'tax' => 1760,
            'service_charge' => 0,
            'total' => 17760,
            'payment_status' => 'unpaid',
            'payment_method' => 'cashier',
            'created_by' => null,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'variant_id' => null,
            'product_name' => $product->name,
            'variant_name' => null,
            'qty' => 2,
            'price' => 8000,
            'subtotal' => 16000,
            'note' => null,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'method' => 'cash',
            'amount' => 0,
            'change_amount' => 0,
            'status' => 'pending',
            'paid_at' => null,
            'created_by' => $cashier->id,
        ]);

        $this->actingAs($cashier)
            ->get(route('cashier.orders.show', $order))
            ->assertOk();

        $this->actingAs($cashier)
            ->patch(route('cashier.orders.approve', $order))
            ->assertRedirect();

        $this->assertSame('accepted', $order->fresh()->status);
    }

    public function test_cashier_can_reject_qr_table_order(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);

        $table = Table::create([
            'code' => 'T002',
            'name' => 'Table 2',
            'is_active' => true,
        ]);

        $order = Order::create([
            'order_number' => 'QR-20260506-0002',
            'table_id' => $table->id,
            'customer_name' => 'Sari',
            'source' => 'qr_table',
            'status' => 'pending',
            'subtotal' => 10000,
            'discount' => 0,
            'tax' => 1100,
            'service_charge' => 0,
            'total' => 11100,
            'payment_status' => 'unpaid',
            'payment_method' => 'cashier',
            'created_by' => null,
        ]);

        $this->actingAs($cashier)
            ->patch(route('cashier.orders.reject', $order))
            ->assertRedirect();

        $this->assertSame('cancelled', $order->fresh()->status);
    }
}
