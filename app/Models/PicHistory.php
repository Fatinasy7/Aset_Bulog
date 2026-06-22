<?php

namespace App\Models;

use App\Models\Asset;
use App\Models\Pic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PicHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'pic_lama_id',
        'pic_baru_id',
        'alasan',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function oldPic()
    {
        return $this->belongsTo(Pic::class, 'pic_lama_id');
    }

    public function newPic()
    {
        return $this->belongsTo(Pic::class, 'pic_baru_id');
    }
}
