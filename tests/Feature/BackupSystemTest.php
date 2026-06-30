<?php

namespace Tests\Feature;

use App\Services\DatabaseBackupService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BackupSystemTest extends TestCase
{
    public function test_it_creates_a_database_backup_file_using_a_configured_dump_command(): void
    {
        $scriptPath = sys_get_temp_dir() . '/backup-script-' . uniqid() . '.php';
        file_put_contents($scriptPath, <<<'PHP'
<?php
$outputPath = $argv[1] ?? null;
if ($outputPath === null) {
    fwrite(STDERR, "missing output path\n");
    exit(1);
}
file_put_contents($outputPath, "-- backup sql\nCREATE TABLE test (id INT);\n");
PHP);

        config()->set('backup.dump_command', 'php ' . escapeshellarg($scriptPath) . ' {output}');

        $outputPath = storage_path('app/testing/backup-test.sql');
        @mkdir(dirname($outputPath), 0777, true);

        $service = app(DatabaseBackupService::class);
        $result = $service->createBackup($outputPath);

        $this->assertTrue($result['created']);
        $this->assertFileExists($result['path']);
        $this->assertStringContainsString('CREATE TABLE', file_get_contents($result['path']));
    }

    public function test_it_can_verify_database_integrity_for_existing_tables(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO SQLite driver is not available.');
        }

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        DB::purge('sqlite');
        $connection = DB::connection('sqlite');
        $connection->getSchemaBuilder()->create('users', function ($table): void {
            $table->id();
        });
        $connection->getSchemaBuilder()->create('assets', function ($table): void {
            $table->id();
        });
        $connection->getSchemaBuilder()->create('pics', function ($table): void {
            $table->id();
        });

        $service = app(DatabaseBackupService::class);
        $result = $service->verifyIntegrity();

        $this->assertTrue($result['passes']);
        $this->assertTrue($result['tables']['users']['exists']);
        $this->assertTrue($result['tables']['assets']['exists']);
        $this->assertTrue($result['tables']['pics']['exists']);
    }
}
