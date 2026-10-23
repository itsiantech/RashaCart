<?php

namespace Tests\Feature;

use App\Models\Settings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'customer']);
        foreach (['customer_login_enabled' => '1', 'customer_login_identifier' => 'both', 'customer_google_enabled' => '1'] as $k => $v) {
            Settings::updateOrCreate(['key' => $k], ['value' => $v]);
        }
        Settings::clearCache();
    }

    public function test_customer_can_register_with_email_and_phone(): void
    {
        $res = $this->postJson('/customer/register', [
            'name' => 'Test Buyer', 'email' => 'buyer@test.com',
            'phone' => '01711111111', 'password' => 'password123',
        ]);
        $res->assertCreated()->assertJson(['message' => 'Registration successful']);
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'buyer@test.com', 'role' => 'customer']);
    }

    public function test_register_rejects_duplicate_email(): void
    {
        User::create(['name' => 'A', 'email' => 'dup@test.com', 'phone' => '01722222222', 'password' => 'secret123', 'role' => 'customer']);
        $this->postJson('/customer/register', [
            'name' => 'B', 'email' => 'dup@test.com', 'phone' => '01733333333', 'password' => 'password123',
        ])->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_customer_can_login_with_email_and_get_safe_redirect(): void
    {
        $user = User::create(['name' => 'Buyer', 'email' => 'login@test.com', 'phone' => '01744444444', 'password' => 'password123', 'role' => 'customer']);
        $user->assignRole('customer');
        $res = $this->postJson('/customer/login', [
            'identifier' => 'login@test.com', 'password' => 'password123', 'return_url' => '/frontend/checkout',
        ]);
        $res->assertOk()->assertJson(['message' => 'Login successful', 'redirect' => '/frontend/checkout']);
    }

    public function test_login_blocks_open_redirect(): void
    {
        $user = User::create(['name' => 'Buyer', 'email' => 'evil@test.com', 'phone' => '01755555555', 'password' => 'password123', 'role' => 'customer']);
        $user->assignRole('customer');
        $res = $this->postJson('/customer/login', [
            'identifier' => 'evil@test.com', 'password' => 'password123', 'return_url' => 'https://evil.com/steal',
        ]);
        $res->assertOk();
        $this->assertStringStartsWith('/', $res->json('redirect'));
        $this->assertStringNotContainsString('evil.com', $res->json('redirect'));
    }

    public function test_login_rejects_wrong_password(): void
    {
        $user = User::create(['name' => 'Buyer', 'email' => 'wrong@test.com', 'phone' => '01766666666', 'password' => 'password123', 'role' => 'customer']);
        $user->assignRole('customer');
        $this->postJson('/customer/login', ['identifier' => 'wrong@test.com', 'password' => 'nope-nope-nope'])
            ->assertStatus(422);
        $this->assertGuest();
    }

    public function test_guest_cannot_open_dashboard(): void
    {
        // Guests hit the auth middleware first → login page (spec: redirect guests to login popup/page).
        $this->get('/account')->assertRedirect('/login');
    }

    public function test_customer_profile_index_redirects_to_dashboard(): void
    {
        $me = User::create(['name' => 'Me', 'email' => 'redir@test.com', 'phone' => '01700000001', 'password' => 'secret', 'role' => 'customer']);
        $me->assignRole('customer');
        // Old admin-layout profile must never render for customers.
        $this->actingAs($me)->get('/profile/index')->assertRedirect(route('customer.dashboard', ['tab' => 'profile']));
    }

    public function test_customer_can_open_dashboard_and_sees_only_own_orders(): void
    {
        $me = User::create(['name' => 'Me', 'email' => 'me@test.com', 'phone' => '01777777777', 'password' => 'secret', 'role' => 'customer']);
        $me->assignRole('customer');
        $other = User::create(['name' => 'Other', 'email' => 'other@test.com', 'phone' => '01788888888', 'password' => 'secret', 'role' => 'customer']);
        $mine = \App\Models\Order::create(['user_id' => $me->id, 'invoice_no' => 'INV-MINE01', 'total_amount' => 100, 'status' => 'pending', 'name' => 'Me', 'phone' => '01777777777', 'shipping_address' => 'Dhaka', 'delivery_zone' => 'inside', 'delivery_charge' => 60, 'payment_method' => 'cod']);
        \App\Models\Order::create(['user_id' => $other->id, 'invoice_no' => 'INV-OTHER01', 'total_amount' => 999, 'status' => 'pending', 'name' => 'Other', 'phone' => '01788888888', 'shipping_address' => 'Dhaka', 'delivery_zone' => 'inside', 'delivery_charge' => 60, 'payment_method' => 'cod']);

        $this->actingAs($me)->get('/account/orders')->assertOk()->assertSee('INV-MINE01')->assertDontSee('INV-OTHER01');
    }

    public function test_disabled_login_returns_403(): void
    {
        Settings::updateOrCreate(['key' => 'customer_login_enabled'], ['value' => '0']);
        Settings::clearCache();
        $this->postJson('/customer/register', ['name' => 'X', 'email' => 'x@test.com', 'phone' => '01799999999', 'password' => 'password123'])
            ->assertForbidden();
    }
}
