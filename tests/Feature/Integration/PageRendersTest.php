<?php

namespace Tests\Feature\Integration;

/**
 * Broad render smoke: every listed page must load for an admin without a 5xx.
 * Cheap, high-leverage coverage — each page exercises its controller, the
 * Blade layout, and the BusinessUtil/ProductUtil/TransactionUtil helpers.
 */
class PageRendersTest extends IntegrationTestCase
{
    /** @dataProvider pages */
    public function test_page_renders(string $uri)
    {
        $response = $this->actingAsAdmin()->get($uri);

        // Accept 200 (rendered) or 302 (redirect) — never a server error.
        $this->assertContains(
            $response->getStatusCode(),
            [200, 302],
            "GET {$uri} returned {$response->getStatusCode()}"
        );
    }

    public static function pages(): array
    {
        return array_map(fn ($u) => [$u], [
            // Dashboard
            '/home',
            // Products
            '/products',
            '/products/create',
            '/brands',
            '/units',
            '/taxonomies?type=product',
            '/group-taxes',
            '/tax-rates',
            '/import-products',
            // Contacts
            '/contacts?type=customer',
            '/contacts?type=supplier',
            '/contacts/create?type=customer',
            '/contacts/create?type=supplier',
            '/customer-group',
            // Purchases
            '/purchases',
            '/purchases/create',
            // Sells
            '/sells',
            '/sells/create',
            '/pos/create',
            // Expenses & accounts
            '/expenses',
            '/expenses/create',
            '/expense-categories',
            // Stock
            '/stock-adjustments',
            '/stock-transfers',
            '/business-location',
            // Settings
            '/business/settings',
            '/invoice-schemes',
            '/invoice-layouts',
            '/discount',
            // Custom optician module
            '/optician-workflow',
            // Reports (exercise TransactionUtil / ProductUtil aggregation)
            '/reports/profit-loss',
            '/reports/stock-report',
            '/reports/tax-report',
            '/reports/expense-report',
            '/reports/items-report',
            '/reports/product-sell-report',
            '/reports/product-purchase-report',
            '/reports/register-report',
            '/reports/sell-payment-report',
            '/reports/purchase-payment-report',
            '/reports/lot-report',
            '/reports/stock-adjustment-report',
            '/account/payment-account-report',
            '/account/trial-balance',
            '/account/balance-sheet',
            '/account/cash-flow',
            // Entity edit / detail views (real IDs from the cloned DB)
            '/products/1/edit',
            '/contacts/1',
        ]);
    }
}
