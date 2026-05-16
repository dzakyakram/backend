<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $query = Location::with('user')
            ->when($request->kategori, fn($q) => $q->byKategori($request->kategori))
            ->when($request->status,   fn($q) => $q->where('status', $request->status))
            ->when($request->search,   fn($q) => $q->where('nama', 'ilike', '%'.$request->search.'%'));

        $locations = $query->latest()->paginate(15)->withQueryString();

        return view('admin.locations.index', compact('locations'));
    }

    public function show(Location $location)
    {
        $location->load('user', 'moderator');
        return view('admin.locations.show', compact('location'));
    }

    public function approve(Location $location)
    {
        $location->update([
            'status'          => 'approved',
            'dimoderasi_oleh' => auth()->id(),
            'dimoderasi_at'   => now(),
            'catatan_moderasi'=> null,
        ]);

        return back()->with('success', "Lokasi \"{$location->nama}\" berhasil disetujui.");
    }

    public function reject(Request $request, Location $location)
    {
        $request->validate(['catatan' => 'nullable|string|max:500']);

        $location->update([
            'status'          => 'rejected',
            'dimoderasi_oleh' => auth()->id(),
            'dimoderasi_at'   => now(),
            'catatan_moderasi'=> $request->catatan,
        ]);

        return back()->with('success', "Lokasi \"{$location->nama}\" telah ditolak.");
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return redirect()->route('admin.locations.index')
            ->with('success', 'Lokasi berhasil dihapus.');
    }
}
