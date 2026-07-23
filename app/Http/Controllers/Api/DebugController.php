<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class DebugController extends Controller
{
    public function config()
    {
        return response()->json([
            'supabase_url'    => config('supabase.url') ?? 'NULL',
            'supabase_key'    => config('supabase.key') ? 'ada' : 'NULL',
            'supabase_bucket' => config('supabase.bucket') ?? 'NULL',
            'php_version'     => phpversion(),
            'laravel_version' => app()->version(),
            'upload_max'      => ini_get('upload_max_filesize'),
            'post_max'        => ini_get('post_max_size'),
            'memory_limit'    => ini_get('memory_limit'),
        ]);
    }

    public function testUpload()
    {
        $result = ['step' => '', 'ok' => false];

        try {
            $tmpFile = tempnam(sys_get_temp_dir(), 'test_');
            file_put_contents($tmpFile, str_repeat("\xFF\xD8\xFF\xE0", 1000));
            $result['step'] = 'created test file';

            $response = Http::withoutVerifying()
                ->timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('supabase.key'),
                    'apikey'        => config('supabase.key'),
                    'x-upsert'      => 'true',
                ])
                ->withBody(file_get_contents($tmpFile), 'image/jpeg')
                ->put(config('supabase.url') . '/storage/v1/object/' . config('supabase.bucket') . '/test/upload-check.jpg');

            @unlink($tmpFile);

            $result['step'] = 'uploaded to supabase';
            $result['supabase_status'] = $response->status();
            $result['supabase_body'] = $response->json() ?? $response->body();

            if ($response->successful()) {
                $deleteResponse = Http::withoutVerifying()
                    ->timeout(10)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . config('supabase.key'),
                    ])
                    ->delete(config('supabase.url') . '/storage/v1/object/' . config('supabase.bucket') . '/test/upload-check.jpg');

                $result['step'] = 'cleanup done';
                $result['delete_status'] = $deleteResponse->status();
                $result['ok'] = true;
                $result['message'] = 'Supabase upload + delete BERHASIL';
            } else {
                $result['message'] = 'Supabase upload GAGAL — cek supabase_body';
            }
        } catch (\Throwable $e) {
            $result['exception'] = get_class($e);
            $result['message'] = $e->getMessage();
            $result['step'] = 'error: ' . ($result['step'] ?? 'unknown');
        }

        return response()->json($result, $result['ok'] ? 200 : 500);
    }
}