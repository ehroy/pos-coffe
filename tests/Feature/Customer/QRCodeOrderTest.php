<?php

namespace Tests\Feature\Customer;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QRCodeOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_menu_by_qr_token(): void
    {
        $table = Table::create([
            'code' => 'T001',
            'name' => 'Table 1',
            'is_active' => true,
        ]);

        $response = $this->get(route('customer.table.menu', $table->qr_token));

        $response->assertOk();
    }

    public function test_customer_can_submit_qr_table_order(): void
    {
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

        $response = $this->post(route('customer.table.orders.store', $table->qr_token), [
            'customer_name' => 'Budi',
            'payment_method' => 'cashier',
            'note' => 'Less sugar',
            'items' => [
                [
                    'product_id' => $product->id,
                    'variant_id' => null,
                    'product_name' => $product->name,
                    'variant_name' => 'Default',
                    'qty' => 2,
                    'price' => 8000,
                ],
            ],
        ]);

        $order = Order::first();

        $response->assertRedirect(route('customer.orders.show', ['order' => $order]));
        $this->assertSame('qr_table', $order->source);
        $this->assertSame($table->id, $order->table_id);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertSame('cashier', $order->payment_method);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'qty' => 2,
        ]);
    }

    public function test_qris_qr_table_payment_can_be_confirmed_and_sent_to_kitchen(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);

        $table = Table::create([
            'code' => 'T003',
            'name' => 'Table 3',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Coffee',
            'type' => 'drink',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Latte',
            'slug' => 'latte',
            'description' => 'Test product',
            'base_price' => 10000,
            'is_active' => true,
            'is_stock_tracked' => false,
        ]);

        $order = Order::create([
            'order_number' => 'QR-20260506-0099',
            'table_id' => $table->id,
            'customer_name' => 'Budi',
            'source' => 'qr_table',
            'status' => 'pending',
            'subtotal' => 10000,
            'discount' => 0,
            'tax' => 1100,
            'service_charge' => 0,
            'total' => 11100,
            'payment_status' => 'unpaid',
            'payment_method' => 'qris',
            'created_by' => null,
        ]);

        $response = $this->actingAs($cashier)->post(route('cashier.orders.confirm-payment', $order));

        $response->assertRedirect();

        $order = $order->fresh();

        $this->assertSame('accepted', $order->status);
        $this->assertSame('paid', $order->payment_status);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'method' => 'qris',
            'status' => 'paid',
        ]);
    }
}
