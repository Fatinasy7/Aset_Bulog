<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\User;
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

    public function reportsIndex()
    {
        $assets = Asset::latest()->get();
        $summary = [
            'total_assets' => $assets->count(),
            'total_active' => $assets->where('kondisi', 'Baik')->count(),
            'total_printers' => $assets->where('jenis', 'printer')->count(),
            'avg_depreciation' => 15.4,
        ];

        return view('reports.index', compact('assets', 'summary'));
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