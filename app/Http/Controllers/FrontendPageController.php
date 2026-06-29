<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\User;

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

    public function picsForm()
    {
        $pics = User::where('role', 'pic')->orderBy('name')->get();

        return view('pics.form', compact('pics'));
    }

    public function reportsIndex()
    {
        $assets = Asset::latest()->get();

        return view('reports.index', compact('assets'));
    }

    public function auditIndex()
    {
        $logs = AuditLog::latest()->get();

        return view('audit.index', compact('logs'));
    }
}