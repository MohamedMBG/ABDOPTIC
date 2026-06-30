<?php

namespace Tests\Unit;

use App\Http\Controllers\OpticianWorkflowController;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * The status KEYS are persisted to transactions.optician_status. Renaming or
 * dropping a key orphans existing rows and breaks the kanban grouping. Labels
 * are display-only and may change freely. This test pins the keys.
 */
class OpticianStatusesTest extends TestCase
{
    private function statuses(): array
    {
        $m = new ReflectionMethod(OpticianWorkflowController::class, 'statuses');
        $m->setAccessible(true);

        return $m->invoke(new OpticianWorkflowController());
    }

    public function test_status_keys_are_stable_and_ordered()
    {
        $expected = [
            'prescription_received',
            'lenses_ordered',
            'in_assembly',
            'ready_for_pickup',
            'delivered',
        ];

        $this->assertSame($expected, array_keys($this->statuses()));
    }

    public function test_every_status_has_a_non_empty_label()
    {
        foreach ($this->statuses() as $key => $label) {
            $this->assertIsString($label);
            $this->assertNotSame('', trim($label), "Status {$key} has an empty label");
        }
    }
}
