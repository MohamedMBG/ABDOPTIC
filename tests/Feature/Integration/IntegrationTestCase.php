<?php

namespace Tests\Feature\Integration;

use App\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Base class for DB-backed feature tests.
 *
 * UltimatePOS migrations use raw MySQL DDL (ENUM / MODIFY COLUMN) that sqlite
 * can't run, so these tests target a real MySQL database `abdoptic_test`
 * (a clone of the dev DB). Each test runs inside a transaction that is rolled
 * back afterwards, so the fixture data is never mutated.
 *
 * Setup (one-time, already done):
 *   mysqldump fixing_flaws2 | mysql abdoptic_test
 */
abstract class IntegrationTestCase extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Blade layout + helpers.php read raw $_SERVER superglobals (REMOTE_ADDR,
        // HTTP_USER_AGENT) that the test client doesn't populate. Set them here.
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'PHPUnit';
        $this->withHeader('User-Agent', 'PHPUnit');
    }

    public function createApplication()
    {
        $app = require __DIR__.'/../../../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        // Point the suite at the MySQL test DB regardless of phpunit.xml's sqlite default.
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql.host', '127.0.0.1');
        $app['config']->set('database.connections.mysql.port', '3306');
        $app['config']->set('database.connections.mysql.database', 'abdoptic_test');
        $app['config']->set('database.connections.mysql.username', 'root');
        $app['config']->set('database.connections.mysql.password', '');

        return $app;
    }

    protected function admin(): User
    {
        return User::where('business_id', 1)->orderBy('id')->firstOrFail();
    }

    protected function actingAsAdmin(): static
    {
        $this->actingAs($this->admin());

        return $this;
    }
}
