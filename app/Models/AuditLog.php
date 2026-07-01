<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'asset_code',
        'action',
        'field_name',
        'old_value',
        'new_value',
        'changed_by',
    ];
}