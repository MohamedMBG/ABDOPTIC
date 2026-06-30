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

    /**
     * State-changing endpoints must no longer be reachable with GET.
     * 405 (Method Not Allowed) is resolved at the routing layer, before the
     * auth middleware runs, so these assertions hold without a logged-in user.
     *
     * @dataProvider mutatingGetRoutes
     */
    public function test_state_changing_routes_reject_get($uri)
    {
        $response = $this->get($uri);

        $response->assertStatus(405);
    }

    public static function mutatingGetRoutes()
    {
        return [
            'delete media' => ['/delete-media/1'],
            'activate product' => ['/products/activate/1'],
            'convert to draft' => ['/sells/convert-to-draft/1'],
            'convert to proforma' => ['/sells/convert-to-proforma/1'],
            'toggle subscription' => ['/toggle-subscription/1'],
            'reset mapping' => ['/reset-mapping'],
            'delete backup' => ['/backup/1/delete'],
            'close account' => ['/account/close/1'],
            'activate account' => ['/account/activate/1'],
            'delete account transaction' => ['/account/delete-account-transaction/1'],
            'regenerate modules' => ['/regenerate'],
            'upload module' => ['/upload-module'],
        ];
    }

    public function test_module_upload_route_is_post_only()
    {
        $route = app('router')->getRoutes()->match(
            \Illuminate\Http\Request::create('/upload-module', 'POST')
        );

        $this->assertSame(
            'App\Http\Controllers\Install\ModulesController@uploadModule',
            $route->getActionName()
        );
    }
}
