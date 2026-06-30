<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Route contracts for the custom Optician (Lab) module and the purchase
 * status endpoint. These resolve at the routing layer (before auth), so they
 * need no logged-in user or live database — matching SmokeRoutesTest.
 */
class OpticianWorkflowRoutesTest extends TestCase
{
    private function actionFor(string $uri, string $method = 'GET'): string
    {
        return app('router')->getRoutes()
            ->match(Request::create($uri, $method))
            ->getActionName();
    }

    public function test_workflow_index_route_is_registered()
    {
        $this->assertSame(
            'App\Http\Controllers\OpticianWorkflowController@index',
            $this->actionFor('/optician-workflow')
        );
    }

    public function test_update_status_modal_route_is_registered()
    {
        $this->assertSame(
            'App\Http\Controllers\OpticianWorkflowController@updateStatusModal',
            $this->actionFor('/optician-workflow/update-status-modal/1')
        );
    }

    public function test_update_status_is_post_only()
    {
        // POST resolves to the controller...
        $this->assertSame(
            'App\Http\Controllers\OpticianWorkflowController@updateStatus',
            $this->actionFor('/optician-workflow/update-status/1', 'POST')
        );

        // ...and GET is rejected at the routing layer.
        $this->get('/optician-workflow/update-status/1')->assertStatus(405);
    }

    /**
     * Regression: the purchase status modal calls action([PurchaseController,
     * 'updateStatus']); the missing route made /purchases throw on every load.
     */
    public function test_purchase_update_status_route_exists()
    {
        // Note: GET /purchases/update-status is swallowed by the existing
        // GET /purchases/{id} wildcard, so we only pin the POST resolution.
        $this->assertSame(
            'App\Http\Controllers\PurchaseController@updateStatus',
            $this->actionFor('/purchases/update-status', 'POST')
        );
    }
}
