<?php

namespace App\Models;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pic extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jabatan',
        'email',
        'telepon',
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
