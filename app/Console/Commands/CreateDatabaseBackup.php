<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;

class CreateDatabaseBackup extends Command
{
    protected $signature = 'app:create-database-backup {--output=}';

    protected $description = 'Create a database backup file for the current application database.';

    public function __construct(protected DatabaseBackupService $backupService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $outputPath = $this->option('output');
        $result = $this->backupService->createBackup($outputPath);

        $this->info('Backup database berhasil dibuat.');
        $this->line('Path: ' . $result['path']);
        $this->line('Ukuran: ' . $result['size'] . ' bytes');

        return self::SUCCESS;
    }
}
