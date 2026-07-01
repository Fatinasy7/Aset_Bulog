<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Process;

class DatabaseBackupService
{
    public function createBackup(?string $outputPath = null): array
    {
        $outputPath ??= $this->resolveOutputPath();
        $directory = dirname($outputPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $process = $this->buildDumpProcess($outputPath);
        $process->run();

        if (! $process->isSuccessful() || ! is_file($outputPath) || filesize($outputPath) === 0) {
            throw new RuntimeException('Gagal membuat backup database. ' . trim($process->getErrorOutput() ?: $process->getOutput() ?: 'Tidak ada output yang dihasilkan.'));
        }

        return [
            'created' => true,
            'path' => $outputPath,
            'filename' => basename($outputPath),
            'size' => filesize($outputPath),
        ];
    }

    public function verifyIntegrity(): array
    {
        $connection = DB::connection();
        $tables = ['users', 'assets', 'pics'];
        $results = [];

        foreach ($tables as $table) {
            $exists = $connection->getSchemaBuilder()->hasTable($table);
            $results[$table] = [
                'exists' => $exists,
                'status' => $exists ? 'ok' : 'missing',
            ];
        }

        return [
            'passes' => collect($results)->every(fn (array $item) => $item['exists']),
            'tables' => $results,
        ];
    }

    protected function resolveOutputPath(): string
    {
        $disk = config('backup.storage_disk', 'local');
        $directory = config('backup.storage_path', 'backups');
        $filename = sprintf('db-backup-%s.sql', now()->format('Ymd_His'));

        $path = $directory . '/' . $filename;

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }

        if ($disk === 'local') {
            return storage_path('app/' . $path);
        }

        return $path;
    }

    protected function buildDumpProcess(string $outputPath): Process
    {
        $configuredCommand = trim((string) config('backup.dump_command', ''));
        if ($configuredCommand !== '') {
            $configuredCommand = str_replace('{output}', $outputPath, $configuredCommand);
            $configuredCommand = str_replace('{database}', config('database.connections.mysql.database', 'laravel'), $configuredCommand);
            $configuredCommand = str_replace('{host}', config('database.connections.mysql.host', '127.0.0.1'), $configuredCommand);
            $configuredCommand = str_replace('{port}', config('database.connections.mysql.port', '3307'), $configuredCommand);
            $configuredCommand = str_replace('{username}', config('database.connections.mysql.username', 'root'), $configuredCommand);
            $configuredCommand = str_replace('{password}', config('database.connections.mysql.password', ''), $configuredCommand);

            return Process::fromShellCommandline($configuredCommand);
        }

        $binary = str_replace('\\', '/', 'C:/laragon/bin/mysql/mysql-8.0.30-winx64/bin/mysqldump.exe');

        return new Process([
            $binary,
            '--user=' . config('database.connections.mysql.username', 'root'),
            '--password=' . config('database.connections.mysql.password', ''),
            '--host=' . config('database.connections.mysql.host', '127.0.0.1'),
            '--port=' . config('database.connections.mysql.port', '3307'),
            '--result-file=' . str_replace('\\', '/', $outputPath),
            config('database.connections.mysql.database', 'laravel'),
        ]);
    }
}
