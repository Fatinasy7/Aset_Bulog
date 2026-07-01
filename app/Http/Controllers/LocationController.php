<?php

namespace App\Http\Controllers;

use App\Models\Asset;

class LocationController extends Controller
{
    public function show(Asset $asset)
    {
        return response()->json([
            'assetId' => $asset->id,
            'lokasi' => $asset->lokasi,
            'latitude' => $asset->koordinat_lat,
            'longitude' => $asset->koordinat_lng,
            'lastScan' => [
                'latitude' => $asset->koordinat_lat,
                'longitude' => $asset->koordinat_lng,
                'scanned_at' => $asset->updated_at?->format('Y-m-d H:i:s'),
            ],
        ]);
    }
}
