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
        $query = $this->buildReportQuery($request);
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
            $assets = $this->buildReportQuery($request)->get();
        }

        return $this->downloadPdfResponse($assets);
    }

    public function reportsDownload(Request $request)
    {
        return $this->downloadPdf($request);
    }

    /**
     * Alias untuk index() - digunakan di routes
     */
    public function assets(Request $request)
    {
        return $this->index($request);
    }

    protected function buildReportQuery(Request $request)
    {
        $query = Asset::query()->with('pic:id,nama,jabatan,email')->orderBy('created_at', 'desc');

        if ($search = $request->input('search')) {
            $query->where(function ($sub) use ($search) {
                $sub->where('kode_aset', 'like', "%{$search}%")
                    ->orWhere('nama_aset', 'like', "%{$search}%")
                    ->orWhere('merk_type', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($condition = $request->input('condition')) {
            $query->where('kondisi', $condition);
        }

        if ($location = $request->input('location')) {
            $query->where('lokasi', $location);
        }

        if ($type = $request->input('type')) {
            $query->where('jenis', $type);
        }

        if ($pic = $request->input('pic')) {
            $query->whereHas('pic', function ($sub) use ($pic) {
                $sub->where('nama', 'like', "%{$pic}%");
            });
        }

        if ($from = $request->input('date_from')) {
            $query->whereDate('tgl_perolehan', '>=', $from);
        }

        if ($to = $request->input('date_to')) {
            $query->whereDate('tgl_perolehan', '<=', $to);
        }

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

        return $query;
    }

    protected function downloadPdfResponse($assets)
    {
        $pdf = Pdf::loadView('reports.assets', [
            'assets' => $assets,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('aset-report-' . now()->format('Ymd-His') . '.pdf');
    }
}
