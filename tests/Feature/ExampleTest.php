<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\SubscriptionPackage;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Table;
use App\Models\Area;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_can_render_customer_menu_page(): void
    {
        // Setup packages
        $pkg = SubscriptionPackage::create([
            'name' => 'Monthly Trial',
            'price' => 10.00,
            'billing_period' => 'monthly',
            'features' => ['Feature 1']
        ]);

        $owner = User::create([
            'name' => 'John Martin',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $restaurant = Restaurant::create([
            'name' => 'Riverbend Bistro',
            'slug' => 'riverbend-bistro',
            'user_id' => $owner->id,
            'subscription_package_id' => $pkg->id,
            'subscription_status' => 'active',
            'subscription_expires_at' => now()->addDays(30)
        ]);

        $response = $this->get('/r/riverbend-bistro');
        $response->assertStatus(200);
        $response->assertSee('Riverbend Bistro');
    }

    public function test_customer_can_place_order_successfully(): void
    {
        // Setup packages
        $pkg = SubscriptionPackage::create([
            'name' => 'Monthly Trial',
            'price' => 10.00,
            'billing_period' => 'monthly',
            'features' => ['Feature 1']
        ]);

        $owner = User::create([
            'name' => 'John Martin',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $restaurant = Restaurant::create([
            'name' => 'Riverbend Bistro',
            'slug' => 'riverbend-bistro',
            'user_id' => $owner->id,
            'subscription_package_id' => $pkg->id,
            'subscription_status' => 'active',
            'subscription_expires_at' => now()->addDays(30)
        ]);

        $category = Category::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Burgers'
        ]);

        $item = MenuItem::create([
            'restaurant_id' => $restaurant->id,
            'category_id' => $category->id,
            'name' => 'Cheese Burger',
            'price' => 8.99,
            'is_available' => true
        ]);

        $area = Area::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Main Hall'
        ]);

        $table = Table::create([
            'restaurant_id' => $restaurant->id,
            'area_id' => $area->id,
            'name' => 'Table 1',
            'capacity' => 2,
            'status' => 'available'
        ]);

        // Place order
        $response = $this->postJson("/r/riverbend-bistro/order", [
            'customer_name' => 'Sam Guest',
            'customer_phone' => '0555123456',
            'type' => 'dine_in',
            'table_id' => $table->id,
            'cart' => [
                ['id' => $item->id, 'quantity' => 2]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Sam Guest',
            'customer_phone' => '0555123456',
            'total_price' => 17.98
        ]);
    }
}
