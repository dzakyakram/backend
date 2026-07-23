<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupabaseStorageService
{
    protected string $url;
    protected string $key;
    protected string $bucket;

    public function __construct()
    {
        $this->url    = config('supabase.url');
        $this->key    = config('supabase.key');
        $this->bucket = config('supabase.bucket');
    }

    public function upload(string $filePath, ?string $bucket = null, ?string $destination = null): array
    {
        $bucket      = $bucket ?? $this->bucket;
        $destination = $destination ?? basename($filePath);

        if (empty($this->url) || empty($this->key)) {
            throw new \RuntimeException(
                'Supabase config belum terbaca. Pastikan SUPABASE_URL dan SUPABASE_SERVICE_KEY sudah di-set di Railway Variables.'
            );
        }

        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

        $response = Http::withoutVerifying()
            ->timeout(30)
            ->withHeaders([
                'Authorization' => "Bearer {$this->key}",
                'apikey'        => $this->key,
                'x-upsert'      => 'true',
            ])
            ->withBody(file_get_contents($filePath), $mimeType)
            ->put("{$this->url}/storage/v1/object/{$bucket}/{$destination}");

        if ($response->failed()) {
            Log::error('Supabase upload failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'dest'   => $destination,
            ]);
            throw new \RuntimeException('Gagal upload ke Supabase: ' . $response->body());
        }

        $publicUrl = "{$this->url}/storage/v1/object/public/{$bucket}/{$destination}";

        return [
            'url'  => $publicUrl,
            'path' => $destination,
        ];
    }

    public function delete(string $path, ?string $bucket = null): bool
    {
        $bucket = $bucket ?? $this->bucket;

        $response = Http::withoutVerifying()
            ->timeout(10)
            ->withHeaders([
                'Authorization' => "Bearer {$this->key}",
            ])
            ->delete("{$this->url}/storage/v1/object/{$bucket}/{$path}");

        if ($response->failed()) {
            Log::error('Supabase delete failed', [
                'path'   => $path,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return false;
        }

        return true;
    }

    public function getPublicUrl(string $path, ?string $bucket = null): string
    {
        $bucket = $bucket ?? $this->bucket;
        return "{$this->url}/storage/v1/object/public/{$bucket}/{$path}";
    }
}