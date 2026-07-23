<?php

namespace App\Console\Commands;

use App\Models\Location;
use App\Services\SupabaseStorageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class MigrateImagesToSupabase extends Command
{
    protected $signature = 'images:migrate-to-supabase';
    protected $description = 'Migrate all images from Cloudinary to Supabase Storage';

    public function handle()
    {
        $supabase = app(SupabaseStorageService::class);
        $locations = Location::whereNotNull('foto_url')->whereNotNull('foto_public_id')->get();

        if ($locations->isEmpty()) {
            $this->info('Tidak ada gambar untuk dimigrasi.');
            return Command::SUCCESS;
        }

        $this->info("Ditemukan {$locations->count()} gambar untuk dimigrasi.");
        $this->newLine();

        $success = 0;
        $failed  = 0;

        foreach ($locations as $index => $location) {
            $this->line("[{$index}/{$locations->count()}] {$location->nama}");

            try {
                $tempFile = tempnam(sys_get_temp_dir(), 'migrate_');

                $response = Http::withoutVerifying()->get($location->foto_url);

                if ($response->failed()) {
                    throw new \RuntimeException("Gagal download: {$response->status()}");
                }

                file_put_contents($tempFile, $response->body());

                $ext = pathinfo(parse_url($location->foto_url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $newPath = 'locations/' . uniqid() . '.' . $ext;

                $upload = $supabase->upload($tempFile, config('supabase.bucket'), $newPath);

                $location->update([
                    'foto_url'       => $upload['url'],
                    'foto_public_id' => $upload['path'],
                ]);

                @unlink($tempFile);

                $this->info("  ✓ Berhasil");
                $success++;
            } catch (\Throwable $e) {
                $this->error("  ✗ Gagal: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Selesai! Berhasil: {$success}, Gagal: {$failed}");

        return Command::SUCCESS;
    }
}
