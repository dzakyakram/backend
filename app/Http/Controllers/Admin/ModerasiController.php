<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class ModerasiController extends Controller
{
    public function index()
    {
        $pending = Location::with('user')
            ->pending()
            ->latest()
            ->paginate(10);

        $riwayat = Location::with(['user','moderator'])
            ->whereIn('status', ['approved','rejected'])
            ->whereNotNull('dimoderasi_at')
            ->latest('dimoderasi_at')
            ->take(20)
            ->get();

        return view('admin.moderasi.index', compact('pending', 'riwayat'));
    }

    public function approve(Request $request, $id)
    {
        $loc = Location::findOrFail($id);
        $loc->update([
            'status'          => 'approved',
            'dimoderasi_oleh' => auth()->id(),
            'dimoderasi_at'   => now(),
        ]);
        return back()->with('success', "\"{$loc->nama}\" disetujui.");
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['catatan' => 'nullable|string|max:500']);
        $loc = Location::findOrFail($id);
        $loc->update([
            'status'          => 'rejected',
            'dimoderasi_oleh' => auth()->id(),
            'dimoderasi_at'   => now(),
            'catatan_moderasi'=> $request->catatan,
        ]);
        return back()->with('success', "\"{$loc->nama}\" ditolak.");
    }
}
