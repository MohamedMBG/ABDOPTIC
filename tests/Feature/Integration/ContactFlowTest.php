<?php

namespace Tests\Feature\Integration;

use App\Contact;

class ContactFlowTest extends IntegrationTestCase
{
    public function test_create_customer_persists_contact()
    {
        $before = Contact::where('business_id', 1)->count();

        $response = $this->actingAsAdmin()->post('/contacts', [
            'type' => 'customer',
            'contact_type_radio' => 'individual',
            'first_name' => 'Test',
            'last_name' => 'Patient',
            'mobile' => '0600112233',
            'email' => 'test.patient.'.uniqid().'@example.test',
        ]);

        $this->assertContains($response->getStatusCode(), [200, 302]);

        $this->assertSame($before + 1, Contact::where('business_id', 1)->count());
        $this->assertDatabaseHas('contacts', [
            'business_id' => 1,
            'first_name' => 'Test',
            'mobile' => '0600112233',
        ]);
    }

    public function test_create_customer_without_first_name_persists_nothing()
    {
        // store() catches the ValidationException and returns an error response
        // rather than throwing; the real guarantee is that no row is written.
        $before = Contact::where('business_id', 1)->count();

        $this->actingAsAdmin()->post('/contacts', [
            'type' => 'customer',
            'mobile' => '0600112233',
        ]);

        $this->assertSame($before, Contact::where('business_id', 1)->count());
    }
}
