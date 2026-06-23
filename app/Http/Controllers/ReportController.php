<?php

namespace App\Http\Controllers;

use App\Exports\AssetsExport;
use App\Models\Asset;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::query()->orderBy('created_at', 'desc');

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
            $pdf = Pdf::loadView('reports.assets', [
                'assets' => $assets,
            ]);

            return $pdf->download('aset-report.pdf');
        }

        return response()->json($assets);
    }
}
