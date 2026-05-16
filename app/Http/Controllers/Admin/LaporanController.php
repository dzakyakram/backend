<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        $stats = [
            'per_kategori' => Location::approved()
                ->selectRaw('kategori, COUNT(*) as total')
                ->groupBy('kategori')
                ->pluck('total', 'kategori'),
            'per_bulan' => Location::approved()
                ->selectRaw("TO_CHAR(created_at,'YYYY-MM') as bulan, COUNT(*) as total")
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get(),
            'total' => Location::approved()->count(),
        ];

        return view('admin.laporan.index', compact('stats'));
    }

    public function exportCsv()
    {
        $locations = Location::with('user')->approved()->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="lokasi_'.date('Ymd').'.csv"',
        ];

        $callback = function () use ($locations) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, ['ID','Nama','Kategori','Latitude','Longitude','Alamat','Deskripsi','Diupload Oleh','Tanggal']);

            foreach ($locations as $loc) {
                fputcsv($handle, [
                    $loc->id, $loc->nama, $loc->kategori,
                    $loc->latitude, $loc->longitude,
                    $loc->alamat, $loc->deskripsi,
                    $loc->user->nama ?? '-',
                    $loc->created_at->format('Y-m-d'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportGeoJson()
    {
        $locations = Location::with('user')->approved()->get();

        $geojson = [
            'type'     => 'FeatureCollection',
            'features' => $locations->map(fn($loc) => [
                'type'     => 'Feature',
                'geometry' => [
                    'type'        => 'Point',
                    'coordinates' => [(float)$loc->longitude, (float)$loc->latitude],
                ],
                'properties' => [
                    'id'        => $loc->id,
                    'nama'      => $loc->nama,
                    'kategori'  => $loc->kategori,
                    'alamat'    => $loc->alamat,
                    'deskripsi' => $loc->deskripsi,
                    'foto_url'  => $loc->foto_url,
                    'uploader'  => $loc->user->nama ?? '-',
                    'tanggal'   => $loc->created_at->toDateString(),
                ],
            ])->values()->all(),
        ];

        return response()->json($geojson)
            ->header('Content-Disposition', 'attachment; filename="lokasi_'.date('Ymd').'.geojson"');
    }
}
