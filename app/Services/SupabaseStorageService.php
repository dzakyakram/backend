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
        $mimeType    = mime_content_type($filePath);
        $contents    = file_get_contents($filePath);

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->key}",
            'Content-Type'  => $mimeType,
        ])->withBody($contents, 'binary')
          ->put("{$this->url}/storage/v1/object/{$bucket}/{$destination}");

        if ($response->failed()) {
            Log::error('Supabase upload failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException('Gagal upload file ke Supabase Storage.');
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

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->key}",
        ])->delete("{$this->url}/storage/v1/object/{$bucket}/{$path}");

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
