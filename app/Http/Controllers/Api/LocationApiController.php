<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LocationApiController extends Controller
{
    // GET /api/v1/locations
    public function index(Request $request)
    {
        $locations = Location::with('user:id,nama')
            ->approved()
            ->when($request->kategori, fn($q) => $q->byKategori($request->kategori))
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $locations,
        ]);
    }

    // GET /api/v1/locations/geojson
    public function geojson()
    {
        $locations = Location::approved()
            ->select('id','nama','kategori','latitude','longitude','alamat','foto_url')
            ->get();

        $features = $locations->map(fn($l) => [
            'type'     => 'Feature',
            'geometry' => [
                'type'        => 'Point',
                'coordinates' => [(float)$l->longitude, (float)$l->latitude],
            ],
            'properties' => [
                'id'       => $l->id,
                'nama'     => $l->nama,
                'kategori' => $l->kategori,
                'alamat'   => $l->alamat,
                'foto_url' => $l->foto_url,
            ],
        ]);

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    // GET /api/v1/locations/radius?lat=&lng=&km=10&kategori=
    public function radius(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'km'  => 'sometimes|numeric|min:0.1|max:100',
        ]);

        $locations = Location::approved()
            ->when($request->kategori, fn($q) => $q->byKategori($request->kategori))
            ->withinRadius(
                (float)$request->lat,
                (float)$request->lng,
                (float)($request->km ?? 10)
            )
            ->get();

        return response()->json([
            'success' => true,
            'radius_km' => (float)($request->km ?? 10),
            'total'   => $locations->count(),
            'data'    => $locations,
        ]);
    }

    // GET /api/v1/locations/{id}
    public function show($id)
    {
        $location = Location::with('user:id,nama')->approved()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $location,
        ]);
    }

    // GET /api/v1/locations/kategori/{kat}
    public function byKategori($kat)
    {
        $locations = Location::approved()->byKategori($kat)->latest()->paginate(20);
        return response()->json(['success' => true, 'data' => $locations]);
    }

    // POST /api/v1/locations  (upload dari Flutter)
    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'kategori'  => 'required|in:wisata,kuliner,hotel',
            'alamat'    => 'nullable|string|max:250',
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'foto'      => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        // Upload foto ke Cloudinary
        $upload = Cloudinary::upload($request->file('foto')->getRealPath(), [
            'folder'         => 'nusantaramap/locations',
            'transformation' => ['quality' => 'auto', 'fetch_format' => 'auto'],
        ]);

        $location = Location::create([
            'nama'          => $request->nama,
            'deskripsi'     => $request->deskripsi,
            'kategori'      => $request->kategori,
            'alamat'        => $request->alamat,
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
            'foto_url'      => $upload->getSecurePath(),
            'foto_public_id'=> $upload->getPublicId(),
            'user_id'       => auth()->id(),
            'status'        => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lokasi berhasil diupload. Menunggu verifikasi.',
            'data'    => $location,
        ], 201);
    }

    // GET /api/v1/locations/my
    public function myLocations(): JsonResponse
    {
        $locations = Location::where('user_id', Auth::id())
            ->withTrashed(false)           // jangan tampilkan yang soft-deleted
            ->select(
                'id', 'nama', 'deskripsi', 'kategori',
                'alamat', 'latitude', 'longitude',
                'foto_url', 'status', 'created_at'
            )
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $locations,
        ]);
    }

    // DELETE /api/v1/locations/{id}
    public function destroy($id)
    {
        $location = Location::where('user_id', auth()->id())->findOrFail($id);

        if ($location->foto_public_id) {
            Cloudinary::destroy($location->foto_public_id);
        }

        $location->delete();

        return response()->json(['success' => true, 'message' => 'Lokasi dihapus.']);
    }

    // POST /api/v1/locations/{id}/bookmark
    public function bookmark($id)
    {
        $location = Location::approved()->findOrFail($id);
        auth()->user()->bookmarks()->syncWithoutDetaching([$location->id]);
        return response()->json(['success' => true, 'message' => 'Ditambahkan ke bookmark.']);
    }

    // DELETE /api/v1/locations/{id}/bookmark
    public function unbookmark($id)
    {
        auth()->user()->bookmarks()->detach($id);
        return response()->json(['success' => true, 'message' => 'Bookmark dihapus.']);
    }

    // GET /api/v1/bookmarks
    public function myBookmarks()
    {
        $bookmarks = auth()->user()->bookmarks()->approved()->latest()->paginate(15);
        return response()->json(['success' => true, 'data' => $bookmarks]);
    }
}
