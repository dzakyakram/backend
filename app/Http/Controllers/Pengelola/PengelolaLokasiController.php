<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class PengelolaLokasiController extends Controller
{
    public function index(Request $request)
    {
        $locations = Location::with('user')
            ->when($request->status,   fn($q) => $q->where('status', $request->status))
            ->when($request->kategori, fn($q) => $q->byKategori($request->kategori))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('pengelola.lokasi.index', compact('locations'));
    }

    public function show($id)
    {
        $location = Location::with('user')->findOrFail($id);
        return view('pengelola.lokasi.show', compact('location'));
    }

    public function approve($id)
    {
        $loc = Location::findOrFail($id);
        $loc->update([
            'status'          => 'approved',
            'dimoderasi_oleh' => auth()->id(),
            'dimoderasi_at'   => now(),
        ]);
        return back()->with('success', "\"{$loc->nama}\" berhasil disetujui dan ditampilkan di peta.");
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['catatan' => 'nullable|string|max:500']);
        $loc = Location::findOrFail($id);
        $loc->update([
            'status'          => 'rejected',
            'catatan_moderasi'=> $request->catatan,
            'dimoderasi_oleh' => auth()->id(),
            'dimoderasi_at'   => now(),
        ]);
        return back()->with('success', "\"{$loc->nama}\" telah ditolak.");
    }
}

