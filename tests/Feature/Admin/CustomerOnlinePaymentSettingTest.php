<?php

namespace Tests\Feature\Admin;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOnlinePaymentSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_disable_qris_for_customer_checkout(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->patch(route('admin.settings.customer-online-payment'), [
                'enabled' => false,
            ])
            ->assertRedirect();

        $this->assertFalse(AppSetting::boolean('customer_online_payment_enabled', true));

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
            'slug' => 'americano-setting-test',
            'description' => 'Test product',
            'base_price' => 8000,
            'is_active' => true,
            'is_stock_tracked' => false,
        ]);

        $response = $this->post(route('customer.table.orders.store', $table->qr_token), [
            'customer_name' => 'Budi',
            'payment_method' => 'qris',
            'items' => [
                [
                    'product_id' => $product->id,
                    'variant_id' => null,
                    'product_name' => $product->name,
                    'variant_name' => null,
                    'qty' => 1,
                    'price' => 8000,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('payment_method');
    }

    public function test_owner_can_open_owner_settings_page(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $this->actingAs($owner)
            ->get(route('owner.settings'))
            ->assertOk();
    }

    public function test_owner_can_update_owner_settings(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $this->actingAs($owner)
            ->patch(route('owner.settings.update'), [
                'customer_online_payment_enabled' => true,
                'payment_gateway_provider' => 'midtrans',
                'payment_gateway_client_key' => 'client-test',
                'payment_gateway_secret_key' => 'secret-test',
                'payment_gateway_merchant_id' => 'merchant-test',
                'printer_name' => 'EPSON TM-T82',
                'printer_ip' => '192.168.1.50',
                'printer_port' => 9100,
                'tax_enabled' => true,
                'tax_rate' => 11,
                'service_charge_enabled' => true,
                'service_charge_rate' => 5,
                'integration_enabled' => true,
                'integration_webhook_url' => 'https://example.com/webhook',
                'integration_api_key' => 'api-test',
                'integration_secret_key' => 'integration-secret',
            ])
            ->assertRedirect();

        $this->assertTrue(AppSetting::boolean('customer_online_payment_enabled', false));
        $this->assertSame('midtrans', AppSetting::getValue('payment_gateway_provider'));
        $this->assertSame('EPSON TM-T82', AppSetting::getValue('printer_name'));
        $this->assertSame('11', AppSetting::getValue('tax_rate'));
        $this->assertSame('5', AppSetting::getValue('service_charge_rate'));
    }
}
