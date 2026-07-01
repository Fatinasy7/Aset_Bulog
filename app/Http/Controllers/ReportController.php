<?php

namespace App\Http\Controllers;

use App\Exports\AssetsExport;
use App\Http\Controllers\Traits\ApiResponseFormatter;
use App\Models\Asset;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    use ApiResponseFormatter;

    public function index(Request $request)
    {
        $query = Asset::query()->with('pic:id,nama,jabatan,email')->orderBy('created_at', 'desc');

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('lokasi')) {
            $query->where('lokasi', 'like', '%' . $request->lokasi . '%');
        }

        if ($request->filled('pic_id')) {
            $query->where('pic_id', $request->pic_id);
        }

        $assets = $query->get();
        $format = strtolower($request->get('format', 'preview'));

        if ($format === 'excel') {
            return Excel::download(new AssetsExport($assets), 'aset-report.xlsx');
        }

        if ($format === 'pdf') {
            return $this->downloadPdf($request, $assets);
        }

        return response()->json($assets->map(fn (Asset $asset) => $this->formatAssetPayload($asset)));
    }

    public function downloadPdf(Request $request, $assets = null)
    {
        if ($assets === null) {
            $query = Asset::query()->with('pic:id,nama,jabatan,email')->orderBy('created_at', 'desc');

            if ($request->filled('kondisi')) {
                $query->where('kondisi', $request->kondisi);
            }

            if ($request->filled('jenis')) {
                $query->where('jenis', $request->jenis);
            }

            if ($request->filled('lokasi')) {
                $query->where('lokasi', 'like', '%' . $request->lokasi . '%');
            }

            if ($request->filled('pic_id')) {
                $query->where('pic_id', $request->pic_id);
            }

            $assets = $query->get();
        }

        return $this->downloadPdfResponse($assets);
    }

    protected function downloadPdfResponse($assets)
    {
        $pdf = Pdf::loadView('reports.assets', [
            'assets' => $assets,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('aset-report-' . now()->format('Ymd-His') . '.pdf');
    }
}
