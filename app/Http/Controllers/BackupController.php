<?php

namespace App\Http\Controllers;

use App\Services\DatabaseBackupService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function __construct(protected DatabaseBackupService $backupService)
    {
    }

    public function index(Request $request)
    {
        $disk = $request->get('disk', config('backup.storage_disk', 'local'));
        $directory = config('backup.storage_path', 'backups');

        $files = Storage::disk($disk)->files($directory);

        return response()->json([
            'disk' => $disk,
            'directory' => $directory,
            'files' => collect($files)->map(fn (string $path) => [
                'path' => $path,
                'size' => Storage::disk($disk)->size($path),
                'updated_at' => Storage::disk($disk)->lastModified($path),
            ])->values(),
        ]);
    }

    public function store(Request $request)
    {
        $outputPath = $request->input('output');
        $result = $this->backupService->createBackup($outputPath);

        return response()->json($result, Response::HTTP_CREATED);
    }

    public function verify()
    {
        return response()->json($this->backupService->verifyIntegrity(), Response::HTTP_OK);
    }
}
