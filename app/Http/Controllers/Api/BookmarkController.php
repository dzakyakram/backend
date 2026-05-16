<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    /**
     * GET /api/v1/bookmarks
     * Ambil semua bookmark milik user yang sedang login,
     * beserta data lokasi lengkap (nama, alamat, kategori, foto, koordinat).
     */
    public function index(): JsonResponse
    {
        $bookmarks = Bookmark::with([
                'location' => function ($q) {
                    $q->select(
                        'id', 'nama', 'deskripsi', 'kategori',
                        'alamat', 'latitude', 'longitude',
                        'foto_url', 'status'
                    );
                }
            ])
            ->where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(function ($bm) {
                // Flatten supaya Flutter mudah konsumsi
                $loc = $bm->location;
                if (!$loc) return null;

                return [
                    'bookmark_id'  => $bm->id,
                    'location_id'  => $loc->id,
                    'id'           => $loc->id,        // alias agar LocationDetailSheet bisa pakai
                    'nama'         => $loc->nama,
                    'deskripsi'    => $loc->deskripsi,
                    'kategori'     => $loc->kategori,
                    'alamat'       => $loc->alamat,
                    'latitude'     => $loc->latitude,
                    'longitude'    => $loc->longitude,
                    'foto_url'     => $loc->foto_url,
                    'status'       => $loc->status,
                    'created_at'   => $bm->created_at,
                ];
            })
            ->filter() // buang null (lokasi terhapus)
            ->values();

        return response()->json([
            'success' => true,
            'data'    => $bookmarks,
        ]);
    }

    /**
     * POST /api/v1/locations/{location}/bookmark
     * Tambah bookmark. Jika sudah ada, kembalikan 200 (idempotent).
     */
    public function store(Location $location): JsonResponse
    {
        $userId = Auth::id();

        // Cegah duplikat — firstOrCreate aman untuk race condition
        $bookmark = Bookmark::firstOrCreate([
            'user_id'     => $userId,
            'location_id' => $location->id,
        ]);

        $created = $bookmark->wasRecentlyCreated;

        return response()->json([
            'success'    => true,
            'message'    => $created ? 'Lokasi berhasil disimpan' : 'Lokasi sudah tersimpan',
            'bookmarked' => true,
        ], $created ? 201 : 200);
    }

    /**
     * DELETE /api/v1/locations/{location}/bookmark
     * Hapus bookmark. Jika tidak ada, tetap kembalikan 200.
     */
    public function destroy(Location $location): JsonResponse
    {
        Bookmark::where('user_id', Auth::id())
                ->where('location_id', $location->id)
                ->delete();

        return response()->json([
            'success'    => true,
            'message'    => 'Bookmark dihapus',
            'bookmarked' => false,
        ]);
    }
}
