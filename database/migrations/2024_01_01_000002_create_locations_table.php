<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->enum('kategori', ['wisata', 'kuliner', 'hotel']);
            $table->string('alamat')->nullable();
            $table->decimal('latitude',  10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('foto_url')->nullable();
            $table->string('foto_public_id')->nullable();   // Cloudinary ID
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan_moderasi')->nullable();
            $table->foreignId('dimoderasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dimoderasi_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Tambahkan kolom geometry PostGIS
        DB::statement('ALTER TABLE locations ADD COLUMN geom extensions.geometry(Point, 4326)');

        // Trigger: auto-update geom dari latitude/longitude
        DB::statement("
            CREATE OR REPLACE FUNCTION sync_location_geom()
            RETURNS TRIGGER AS \$\$
            BEGIN
                NEW.geom := ST_SetSRID(ST_MakePoint(NEW.longitude, NEW.latitude), 4326);
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::statement("
            CREATE TRIGGER trg_sync_geom
            BEFORE INSERT OR UPDATE ON locations
            FOR EACH ROW EXECUTE FUNCTION sync_location_geom();
        ");

        // Index spasial
        DB::statement('CREATE INDEX idx_locations_geom ON locations USING GIST (geom)');
        DB::statement('CREATE INDEX idx_locations_status ON locations (status)');
        DB::statement('CREATE INDEX idx_locations_kategori ON locations (kategori)');
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
