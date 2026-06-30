<?php

namespace Tests\Feature\Integration;

/**
 * DataTables index endpoints serve their rows from the same controller action
 * via the ajax() branch. Hitting them with an XHR header runs the real
 * TransactionUtil / ProductUtil / ContactUtil query builders — the bulk of the
 * business logic that the page shell alone never touches.
 */
class AjaxDataTest extends IntegrationTestCase
{
    /** @dataProvider endpoints */
    public function test_datatable_endpoint_returns_data(string $uri)
    {
        $response = $this->actingAsAdmin()
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->get($uri);

        $this->assertContains(
            $response->getStatusCode(),
            [200, 302],
            "AJAX GET {$uri} returned {$response->getStatusCode()}"
        );
    }

    public static function endpoints(): array
    {
        return array_map(fn ($u) => [$u], [
            '/sells',
            '/purchases',
            '/products',
            '/expenses',
            '/contacts?type=customer',
            '/contacts?type=supplier',
            '/stock-transfers',
            '/stock-adjustments',
            '/account/account',
            '/expense-categories',
            '/sells?order_status=final',
        ]);
    }
}
