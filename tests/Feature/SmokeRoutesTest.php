<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Tests\TestCase;

class SmokeRoutesTest extends TestCase
{
    public function test_homepage_is_reachable_without_a_live_database()
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(config('app.name'));
    }

    public function test_login_page_is_reachable()
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('Welcome Back', false);
    }

    public function test_business_registration_redirects_when_disabled()
    {
        $response = $this->get('/business/register');

        $response->assertRedirect('/');
    }

    public function test_default_user_registration_route_is_disabled()
    {
        $this->assertFalse(app('router')->has('register'));

        $response = $this->get('/register');

        $response->assertNotFound();
    }

    public function test_payment_account_resource_uses_account_controller()
    {
        $route = app('router')->getRoutes()->match(Request::create('/payment-account', 'GET'));

        $this->assertSame('App\Http\Controllers\AccountController@index', $route->getActionName());
    }

    public function test_logout_route_only_allows_post_requests()
    {
        $response = $this->get('/logout');

        $response->assertStatus(405);
    }

    public function test_sign_in_as_user_route_only_allows_post_requests()
    {
        $response = $this->get('/sign-in-as-user/1');

        $response->assertStatus(405);
    }
}
