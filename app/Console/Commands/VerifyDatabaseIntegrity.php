<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;

class VerifyDatabaseIntegrity extends Command
{
    protected $signature = 'app:verify-database-integrity';

    protected $description = 'Verify that critical database tables exist.';

    public function __construct(protected DatabaseBackupService $backupService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $result = $this->backupService->verifyIntegrity();

        foreach ($result['tables'] as $table => $details) {
            $status = $details['exists'] ? 'OK' : 'MISSING';
            $this->line("{$table}: {$status}");
        }

        if ($result['passes']) {
            $this->info('Database integrity verification passed.');
            return self::SUCCESS;
        }

        $this->error('Database integrity verification failed.');
        return self::FAILURE;
    }
}
