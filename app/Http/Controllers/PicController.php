<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ApiResponseFormatter;
use App\Models\Asset;
use App\Models\Pic;
use App\Models\PicHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PicController extends Controller
{
    use ApiResponseFormatter;

    public function index()
    {
        return Pic::orderBy('created_at', 'desc')->get()->map(function (Pic $pic) {
            return $this->formatPicPayload($pic);
        });
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'email' => 'required|email|unique:pics,email',
            'telepon' => 'nullable|string|max:20',
        ]);

        $pic = Pic::create($validated);

        return response()->json($this->formatPicPayload($pic), Response::HTTP_CREATED);
    }

    public function update(Request $request, Pic $pic)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'email' => 'required|email|unique:pics,email,' . $pic->id,
            'telepon' => 'nullable|string|max:20',
        ]);

        $pic->update($validated);

        return response()->json($this->formatPicPayload($pic));
    }

    public function destroy(Pic $pic)
    {
        $pic->delete();

        return response()->json(['message' => 'PIC deleted successfully.']);
    }

    public function assignPic(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'pic_id' => 'required|exists:pics,id',
            'alasan' => 'nullable|string',
        ]);

        if ($asset->kondisi === 'RUSAK_BERAT') {
            return response()->json([
                'message' => 'Aset dengan kondisi RUSAK BERAT tidak dapat dialihkan PIC-nya.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $oldPicId = $asset->pic_id;
        $asset->pic_id = $validated['pic_id'];
        $asset->save();
        $asset->load('pic:id,nama,jabatan,email');

        PicHistory::create([
            'asset_id' => $asset->id,
            'pic_lama_id' => $oldPicId,
            'pic_baru_id' => $validated['pic_id'],
            'alasan' => $validated['alasan'] ?? null,
        ]);

        return response()->json($this->formatAssetPayload($asset));
    }
}
