<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_wisata'    => Location::approved()->byKategori('wisata')->count(),
            'total_kuliner'   => Location::approved()->byKategori('kuliner')->count(),
            'total_hotel'     => Location::approved()->byKategori('hotel')->count(),
            'total_pending'   => Location::pending()->count(),
            'total_pengguna'  => User::where('role', 'pengguna')->count(),
            'total_lokasi'    => Location::approved()->count(),
        ];

        // Upload 7 hari terakhir per kategori
        $uploadChart = Location::select(
                DB::raw("DATE(created_at) as tanggal"),
                DB::raw("kategori"),
                DB::raw("COUNT(*) as total")
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('tanggal', 'kategori')
            ->orderBy('tanggal')
            ->get()
            ->groupBy('tanggal');

        // Aktivitas terbaru
        $aktivitas = Location::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Pending moderasi
        $pending = Location::with('user')
            ->pending()
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'uploadChart', 'aktivitas', 'pending'));
    }

    public function peta()
    {
        $locations = Location::approved()
            ->select('id', 'nama', 'kategori', 'latitude', 'longitude', 'alamat', 'foto_url', 'status')
            ->get();

        return view('admin.peta', compact('locations'));
    }
}
