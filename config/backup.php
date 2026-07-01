<?php

return [
    'storage_disk' => env('BACKUP_STORAGE_DISK', 'local'),
    'storage_path' => env('BACKUP_STORAGE_PATH', 'backups'),
    'dump_command' => env('BACKUP_DUMP_COMMAND'),
];
