<?php

namespace Tests\Unit;

use App\Http\Controllers\Install\ModulesController;
use PHPUnit\Framework\TestCase;

class ModuleZipSafetyTest extends TestCase
{
    /** @dataProvider unsafeEntries */
    public function test_unsafe_zip_entries_are_rejected($entry)
    {
        $this->assertTrue(
            ModulesController::isUnsafeZipEntry($entry, '/var/www/Modules'),
            "Entry should be rejected: {$entry}"
        );
    }

    /** @dataProvider safeEntries */
    public function test_safe_zip_entries_are_allowed($entry)
    {
        $this->assertFalse(
            ModulesController::isUnsafeZipEntry($entry, '/var/www/Modules'),
            "Entry should be allowed: {$entry}"
        );
    }

    public static function unsafeEntries()
    {
        return [
            'parent traversal' => ['../evil.php'],
            'nested traversal' => ['Repair/../../evil.php'],
            'deep traversal' => ['../../../etc/passwd'],
            'absolute unix path' => ['/etc/cron.d/evil'],
            'windows drive path' => ['C:/Windows/System32/evil.dll'],
            'backslash traversal' => ['..\\..\\evil.php'],
            'null byte' => ["good.php\0.zip"],
            'empty name' => [''],
        ];
    }

    public static function safeEntries()
    {
        return [
            'plain file' => ['module.json'],
            'nested file' => ['Repair/composer.json'],
            'deep nested' => ['Repair/Config/config.php'],
            'dotfile' => ['Repair/.gitignore'],
        ];
    }
}
