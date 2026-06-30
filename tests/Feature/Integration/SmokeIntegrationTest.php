<?php

namespace Tests\Feature\Integration;

class SmokeIntegrationTest extends IntegrationTestCase
{
    public function test_harness_connects_to_test_db_and_logs_in()
    {
        $admin = $this->admin();
        $this->assertSame(1, (int) $admin->business_id);

        $this->actingAsAdmin();
        $this->get('/home')->assertOk();
    }

    public function test_contacts_index_loads()
    {
        $this->actingAsAdmin()->get('/contacts?type=customer')->assertOk();
    }
}
