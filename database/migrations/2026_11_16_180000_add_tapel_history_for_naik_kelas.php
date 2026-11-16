<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tapels', function (Blueprint $table) {
            $table->boolean('is_aktif')->default(false)->after('semester');
        });

        $tapelAktif = DB::table('tapels')->max('id');
        if ($tapelAktif) {
            DB::table('tapels')->where('id', $tapelAktif)->update(['is_aktif' => true]);
        }

        Schema::table('kelas', function (Blueprint $table) {
            $table->dropUnique('kelas_name_unique');
            $table->unique(['tapel_id', 'tingkat_id', 'name'], 'kelas_tapel_tingkat_name_unique');
        });

        Schema::table('ekskuls', function (Blueprint $table) {
            $table->dropUnique('ekskuls_name_unique');
            $table->unique(['tapel_id', 'name'], 'ekskuls_tapel_name_unique');
        });

        Schema::create('riwayat_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('tapel_id')->constrained('tapels')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['siswa_id', 'tapel_id']);
        });

        Schema::table('ketidakhadirans', function (Blueprint $table) {
            $table->foreignId('tapel_id')->nullable()->after('siswa_id')->constrained('tapels')->nullOnDelete();
        });

        Schema::table('catatan_walas', function (Blueprint $table) {
            $table->foreignId('tapel_id')->nullable()->after('siswa_id')->constrained('tapels')->nullOnDelete();
        });

        $penempatan = DB::table('siswas')->whereNotNull('kelas_id')->get();
        foreach ($penempatan as $siswa) {
            $tapelId = DB::table('kelas')->where('id', $siswa->kelas_id)->value('tapel_id');
            if (!$tapelId) {
                continue;
            }
            DB::table('riwayat_kelas')->insert([
                'siswa_id' => $siswa->id,
                'kelas_id' => $siswa->kelas_id,
                'tapel_id' => $tapelId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('ketidakhadirans')->where('siswa_id', $siswa->id)->whereNull('tapel_id')->update(['tapel_id' => $tapelId]);
            DB::table('catatan_walas')->where('siswa_id', $siswa->id)->whereNull('tapel_id')->update(['tapel_id' => $tapelId]);
        }
    }

    public function down()
    {
        Schema::table('catatan_walas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tapel_id');
        });
        Schema::table('ketidakhadirans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tapel_id');
        });
        Schema::dropIfExists('riwayat_kelas');
        Schema::table('ekskuls', function (Blueprint $table) {
            $table->dropUnique('ekskuls_tapel_name_unique');
            $table->unique('name');
        });
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropUnique('kelas_tapel_tingkat_name_unique');
            $table->unique('name');
        });
        Schema::table('tapels', function (Blueprint $table) {
            $table->dropColumn('is_aktif');
        });
    }
};
