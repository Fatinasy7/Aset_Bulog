<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FrontendPageController extends Controller
{
    public function dashboard()
    {
        $assets = Asset::latest()->get();
        $pics = User::where('role', 'pic')->orderBy('name')->get();
        $recentAssets = $assets->take(5);
        $problematicAssets = $assets->filter(function (Asset $asset) {
            return in_array($asset->kondisi, ['Rusak Ringan', 'Rusak Berat', 'Dalam Perbaikan'], true);
        })->take(5);

        $summary = [
            'total_assets' => $assets->count(),
            'total_laptops' => $assets->where('jenis', 'laptop')->count(),
            'total_printers' => $assets->where('jenis', 'printer')->count(),
            'total_pics' => $pics->count(),
        ];

        $conditionCounts = $assets->groupBy('kondisi')->map->count()->sortDesc();
        $typeCounts = $assets->groupBy('jenis')->map->count();

        return view('dashboard.index', compact('summary', 'conditionCounts', 'typeCounts', 'assets', 'pics', 'recentAssets', 'problematicAssets'));
    }

    public function assetsIndex()
    {
        $assets = Asset::latest()->get();

        return view('assets.index', compact('assets'));
    }

    public function assetsCreate()
    {
        $pics = User::where('role', 'pic')->orderBy('name')->get();

        return view('assets.create', compact('pics'));
    }

    public function assetsEdit(Asset $asset)
    {
        $pics = User::where('role', 'pic')->orderBy('name')->get();

        return view('assets.edit', compact('asset', 'pics'));
    }

    public function assetShow(Asset $asset)
    {
        $logs = AuditLog::where('asset_id', $asset->id)->latest()->take(5)->get();

        return view('assets.show', compact('asset', 'logs'));
    }

    public function picsIndex()
    {
        $pics = User::where('role', 'pic')->orderBy('name')->get();

        return view('pics.index', compact('pics'));
    }

    public function picsCreate()
    {
        $pics = User::where('role', 'pic')->orderBy('name')->get();

        return view('pics.form', compact('pics'));
    }

    public function picsEdit(User $pic)
    {
        $pics = User::where('role', 'pic')->orderBy('name')->get();

        return view('pics.form', compact('pic', 'pics'));
    }

    public function picsForm()
    {
        $pics = User::where('role', 'pic')->orderBy('name')->get();

        return view('pics.form', compact('pics'));
    }

    public function storePic(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:pic',
        ]);

        User::create(array_merge($validated, [
            'password' => bcrypt('password'),
        ]));

        return redirect()->route('frontend.pics.index')->with('success', 'PIC berhasil ditambahkan.');
    }

    public function updatePic(Request $request, User $pic)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $pic->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:pic',
        ]);

        $pic->update($validated);

        return redirect()->route('frontend.pics.index')->with('success', 'PIC berhasil diperbarui.');
    }

    public function destroyPic(User $pic)
    {
        $pic->delete();

        return redirect()->route('frontend.pics.index')->with('success', 'PIC berhasil dihapus.');
    }

    public function reportsIndex(Request $request)
    {
        $query = Asset::query();

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
            $query->where('pic_name', $pic);
        }

        if ($from = $request->input('date_from')) {
            $query->whereDate('tgl_perolehan', '>=', $from);
        }

        if ($to = $request->input('date_to')) {
            $query->whereDate('tgl_perolehan', '<=', $to);
        }

        $summaryAssets = (clone $query)->get();
        $assets = $query->latest()->paginate(15)->withQueryString();

        $summary = [
            'total_asset_value' => $summaryAssets->sum('harga'),
            'active_assets' => $summaryAssets->where('kondisi', 'Baik')->count(),
            'maintenance_required' => $summaryAssets->whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat', 'Dalam Perbaikan'])->count(),
            'avg_depreciation' => 15.4,
        ];

        $conditionCounts = $summaryAssets->groupBy('kondisi')->map->count()->sortDesc()->toArray();
        $conditions = Asset::select('kondisi')->distinct()->pluck('kondisi')->sort()->values();
        $locations = Asset::select('lokasi')->distinct()->pluck('lokasi')->sort()->values();
        $types = Asset::select('jenis')->distinct()->pluck('jenis')->sort()->values();
        $pics = Asset::select('pic_name')->whereNotNull('pic_name')->distinct()->pluck('pic_name')->sort()->values();

        return view('reports.index', compact('assets', 'summary', 'conditionCounts', 'conditions', 'locations', 'types', 'pics'));
    }

    public function reportsExport(Request $request)
    {
        $query = Asset::query();

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
            $query->where('pic_name', $pic);
        }

        if ($from = $request->input('date_from')) {
            $query->whereDate('tgl_perolehan', '>=', $from);
        }

        if ($to = $request->input('date_to')) {
            $query->whereDate('tgl_perolehan', '<=', $to);
        }

        $assets = $query->latest()->get();
        $filename = 'laporan-aset-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($assets) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Kode Aset', 'Nama Aset', 'Jenis', 'Merk/Type', 'Serial Number', 'Kondisi', 'PIC', 'Lokasi', 'Tanggal Perolehan', 'Harga']);

            foreach ($assets as $asset) {
                fputcsv($handle, [
                    $asset->kode_aset,
                    $asset->nama_aset,
                    ucfirst($asset->jenis),
                    $asset->merk_type,
                    $asset->serial_number,
                    $asset->kondisi,
                    $asset->pic_name ?: '-',
                    $asset->lokasi,
                    optional($asset->tgl_perolehan)->format('Y-m-d') ?? '-',
                    $asset->harga,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function reportsDownload(Request $request)
    {
        $query = Asset::query();

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
            $query->where('pic_name', $pic);
        }

        if ($from = $request->input('date_from')) {
            $query->whereDate('tgl_perolehan', '>=', $from);
        }

        if ($to = $request->input('date_to')) {
            $query->whereDate('tgl_perolehan', '<=', $to);
        }

        $assets = $query->latest()->get();
        $summary = [
            'total_asset_value' => $assets->sum('harga'),
            'active_assets' => $assets->where('kondisi', 'Baik')->count(),
            'maintenance_required' => $assets->whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat', 'Dalam Perbaikan'])->count(),
            'avg_depreciation' => 15.4,
        ];

        $pdf = Pdf::loadView('reports.pdf', compact('assets', 'summary'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-aset-' . now()->format('Ymd-His') . '.pdf');
    }

    public function reportsPdf(Request $request)
    {
        $query = Asset::query();

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
            $query->where('pic_name', $pic);
        }

        if ($from = $request->input('date_from')) {
            $query->whereDate('tgl_perolehan', '>=', $from);
        }

        if ($to = $request->input('date_to')) {
            $query->whereDate('tgl_perolehan', '<=', $to);
        }

        $assets = $query->latest()->get();

        $summary = [
            'total_asset_value' => $assets->sum('harga'),
            'active_assets' => $assets->where('kondisi', 'Baik')->count(),
            'maintenance_required' => $assets->whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat', 'Dalam Perbaikan'])->count(),
            'avg_depreciation' => 15.4,
        ];

        return view('reports.pdf', compact('assets', 'summary'));
    }

    public function dataLaptop()
    {
        $assets = Asset::where('jenis', 'laptop')->latest()->get();
        $summary = [
            'total_assets' => $assets->count(),
            'total_operational' => $assets->where('kondisi', 'Baik')->count(),
            'total_maintenance' => $assets->whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat', 'Dalam Perbaikan'])->count(),
            'critical_issues' => $assets->where('kondisi', 'Rusak Berat')->count(),
        ];

        return view('assets.laptops', compact('assets', 'summary'));
    }

    public function dataPrinter()
    {
        $assets = Asset::where('jenis', 'printer')->latest()->get();
        $summary = [
            'total_assets' => $assets->count(),
            'total_online' => $assets->where('kondisi', 'Baik')->count(),
            'maintenance_required' => $assets->whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat', 'Dalam Perbaikan'])->count(),
            'alerts' => $assets->where('kondisi', 'Rusak Berat')->count(),
        ];

        return view('assets.printers', compact('assets', 'summary'));
    }

    public function settings()
    {
        $users = User::where('role', 'pic')->orderBy('name')->get();
        $assets = Asset::latest()->get();
        $summary = [
            'total_assets' => $assets->count(),
            'total_laptops' => $assets->where('jenis', 'laptop')->count(),
            'total_printers' => $assets->where('jenis', 'printer')->count(),
        ];

        return view('settings.index', compact('users', 'summary'));
    }

    public function auditIndex()
    {
        $logs = AuditLog::latest()->get();

        return view('audit.index', compact('logs'));
    }

    public function scanQr()
    {
        return view('scan-qr');
    }

    public function scanQrLookup(Request $request)
    {
        $validated = $request->validate([
            'qr_text' => 'required|string|max:255',
        ]);

        $query = trim($validated['qr_text']);
        $asset = Asset::where('kode_aset', $query)
            ->orWhere('serial_number', $query)
            ->orWhere('id', $query)
            ->first();

        if (! $asset) {
            return response()->json(['found' => false, 'message' => 'Aset tidak ditemukan.'], 404);
        }

        return response()->json([
            'found' => true,
            'asset' => [
                'id' => $asset->id,
                'kode_aset' => $asset->kode_aset,
                'nama_aset' => $asset->nama_aset,
                'kondisi' => $asset->kondisi,
                'lokasi' => $asset->lokasi,
                'pic' => $asset->pic_name ?: '-',
                'jenis' => ucfirst($asset->jenis),
                'detail_url' => route('frontend.assets.show', $asset),
            ],
        ]);
    }

    public function dashboardManagement()
    {
        $assets = Asset::latest()->get();
        $pics = User::where('role', 'pic')->orderBy('name')->get();

        $summary = [
            'total_assets' => $assets->count(),
            'total_laptops' => $assets->where('jenis', 'laptop')->count(),
            'total_printers' => $assets->where('jenis', 'printer')->count(),
            'total_pics' => $pics->count(),
        ];

        $conditionCounts = $assets->groupBy('kondisi')->map->count()->sortDesc();

        return view('dashboard.management', compact('summary', 'conditionCounts', 'assets'));
    }

}
