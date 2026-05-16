<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class PengelolaModerasiController extends Controller
{
    public function index()
    {
        $antrian = Location::with('user')
            ->pending()
            ->latest()
            ->paginate(10);

        return view('pengelola.moderasi.index', compact('antrian'));
    }

    public function process(Request $request, $id)
    {
        $request->validate([
            'aksi'    => 'required|in:approve,reject',
            'catatan' => 'nullable|string|max:500',
        ]);

        $loc = Location::findOrFail($id);

        $loc->update([
            'status'           => $request->aksi === 'approve' ? 'approved' : 'rejected',
            'catatan_moderasi' => $request->catatan,
            'dimoderasi_oleh'  => auth()->id(),
            'dimoderasi_at'    => now(),
        ]);

        return back()->with('success', 'Lokasi berhasil diproses.');
    }
}
