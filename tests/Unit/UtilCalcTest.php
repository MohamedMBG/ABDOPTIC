<?php

namespace Tests\Unit;

use App\Utils\Util;
use PHPUnit\Framework\TestCase;

/**
 * Pure-math units of App\Utils\Util — the percentage helpers every price /
 * tax / discount calculation in the app routes through. No DB, no app boot.
 */
class UtilCalcTest extends TestCase
{
    private Util $util;

    protected function setUp(): void
    {
        parent::setUp();
        $this->util = new Util();
    }

    public function test_calc_percentage_adds_percent_of_number()
    {
        $this->assertEqualsWithDelta(15.0, $this->util->calc_percentage(100, 15), 0.0001);
        $this->assertEqualsWithDelta(115.0, $this->util->calc_percentage(100, 15, 100), 0.0001);
        $this->assertEqualsWithDelta(0.0, $this->util->calc_percentage(0, 15), 0.0001);
    }

    public function test_calc_percentage_base_reverses_inclusive_tax()
    {
        // 110 inc. 10% tax -> base 100
        $this->assertEqualsWithDelta(100.0, $this->util->calc_percentage_base(110, 10), 0.0001);
        // round trip: base then add percentage back
        $base = $this->util->calc_percentage_base(125, 25);
        $this->assertEqualsWithDelta(125.0, $this->util->calc_percentage($base, 25, $base), 0.0001);
    }

    public function test_get_percent_returns_growth_percent_and_guards_zero()
    {
        $this->assertEqualsWithDelta(25.0, $this->util->get_percent(100, 125), 0.0001);
        $this->assertEqualsWithDelta(-50.0, $this->util->get_percent(100, 50), 0.0001);
        // division-by-zero guard
        $this->assertSame(0, $this->util->get_percent(0, 125));
    }
}
