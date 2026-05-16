<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama',
        'deskripsi',
        'kategori',
        'alamat',
        'latitude',
        'longitude',
        'foto_url',
        'foto_public_id',
        'user_id',
        'status',
        'catatan_moderasi',
        'dimoderasi_oleh',
        'dimoderasi_at',
    ];

    protected $casts = [
        'latitude'       => 'float',
        'longitude'      => 'float',
        'dimoderasi_at'  => 'datetime',
    ];

    // ── Relasi ────────────────────────────────────────────────

    /**
     * User yang mengupload lokasi ini
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Moderator yang memoderasi lokasi ini (bisa null jika belum dimoderasi)
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dimoderasi_oleh');
    }

    /**
     * Semua bookmark untuk lokasi ini
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    // ── Helper: apakah user ini sudah bookmark? ──────────────

    /**
     * Cek apakah user tertentu sudah bookmark lokasi ini.
     * Berguna jika suatu saat ingin append field is_bookmarked di response.
     *
     * Contoh pemakaian di controller:
     *   $location->isBookmarkedBy(Auth::id())
     */
    public function isBookmarkedBy(int $userId): bool
    {
        return $this->bookmarks()->where('user_id', $userId)->exists();
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }
}
