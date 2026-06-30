<?php

namespace Tests\Unit;

use App\Http\Controllers\BarcodeScanController;
use PHPUnit\Framework\TestCase;

class BarcodeScanStockStatusTest extends TestCase
{
    public function test_stock_status_boundaries()
    {
        // Service / non-stock product -> always sellable.
        $this->assertSame('in_stock', BarcodeScanController::stockStatus(false, 0, 5));

        // Out of stock.
        $this->assertSame('out_of_stock', BarcodeScanController::stockStatus(true, 0, 5));
        $this->assertSame('out_of_stock', BarcodeScanController::stockStatus(true, -2, 5));

        // Low stock: at or below min (and min > 0).
        $this->assertSame('low_stock', BarcodeScanController::stockStatus(true, 5, 5));
        $this->assertSame('low_stock', BarcodeScanController::stockStatus(true, 3, 5));

        // In stock: above min, or no min set.
        $this->assertSame('in_stock', BarcodeScanController::stockStatus(true, 6, 5));
        $this->assertSame('in_stock', BarcodeScanController::stockStatus(true, 1, 0));
    }
}
