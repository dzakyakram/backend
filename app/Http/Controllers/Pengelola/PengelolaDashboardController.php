<?php
// ── PengelolaDashboardController.php ───────────────────────

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\Location;

class PengelolaDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'pending'  => Location::pending()->count(),
            'approved' => Location::approved()->count(),
            'rejected' => Location::where('status','rejected')->count(),
            'my_mod'   => Location::where('dimoderasi_oleh', auth()->id())->count(),
        ];

        $antrian = Location::with('user')->pending()->latest()->take(8)->get();

        return view('pengelola.dashboard', compact('stats', 'antrian'));
    }
}
