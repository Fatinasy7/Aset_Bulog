<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Pic;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function summary(): JsonResponse
    {
        $totalAssets = Asset::count();
        $totalLaptops = Asset::where('jenis', 'laptop')->count();
        $totalPrinters = Asset::where('jenis', 'printer')->count();
        $totalPics = Pic::count();

        $conditionCounts = Asset::selectRaw('UPPER(kondisi) as kondisi, COUNT(*) as total')
            ->groupByRaw('UPPER(kondisi)')
            ->pluck('total', 'kondisi')
            ->toArray();

        $conditionCounts = array_merge([
            'BAIK' => 0,
            'RUSAK_RINGAN' => 0,
            'RUSAK_BERAT' => 0,
            'DALAM_PERBAIKAN' => 0,
            'TIDAK_AKTIF' => 0,
        ], $conditionCounts);

        $latestAssets = Asset::with('pic:id,nama')
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function (Asset $asset) {
                return [
                    'id' => $asset->id,
                    'kodeAset' => $asset->kode_aset,
                    'namaAset' => $asset->nama_aset,
                    'kondisi' => $asset->kondisi,
                    'lokasi' => $asset->lokasi,
                    'pic' => $asset->pic ? [
                        'id' => $asset->pic->id,
                        'nama' => $asset->pic->nama,
                    ] : null,
                    'createdAt' => $asset->created_at?->toISOString(),
                ];
            });

        return response()->json([
            'totalAssets' => $totalAssets,
            'totalLaptops' => $totalLaptops,
            'totalPrinters' => $totalPrinters,
            'totalPics' => $totalPics,
            'conditionCounts' => $conditionCounts,
            'latestAssets' => $latestAssets,
        ]);
    }
}
