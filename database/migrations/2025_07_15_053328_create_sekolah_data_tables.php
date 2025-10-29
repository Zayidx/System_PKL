<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('angkatan', function (Blueprint $table) {
            $table->tinyIncrements('id_angkatan');
            $table->year('tahun');
        });

        Schema::create('jurusan', function (Blueprint $table) {
            $table->tinyIncrements('id_jurusan');
            $table->string('nama_jurusan_lengkap', 50);
            $table->string('nama_jurusan_singkat', 10);
        });

        Schema::create('guru', function (Blueprint $table) {
            $table->increments('nip_guru');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('nama_guru', 60);
            $table->string('kontak_guru', 17);
        });

        // Penyesuaian total pada tabel kepala program
        Schema::create('kepala_program', function (Blueprint $table) {
            $table->increments('nip_kepala_program');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('id_jurusan');
            $table->string('nama_kepala_program', 60);

            $table->foreign('id_jurusan')->references('id_jurusan')->on('jurusan')->onDelete('cascade');
        });

        Schema::table('jurusan', function (Blueprint $table) {
            $table->unsignedInteger('kepala_program')->nullable();
            $table->foreign('kepala_program')->references('nip_kepala_program')->on('kepala_program')->onDelete('set null');
        });

        Schema::create('staff_hubin', function (Blueprint $table) {
            $table->increments('nip_staff');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_staff', 60);
        });

        // Penyesuaian kritis pada tabel kepala sekolah
        Schema::create('kepala_sekolah', function (Blueprint $table) {
            $table->tinyIncrements('id_kepsek');
            // BARU: Tambahkan foreign key ke tabel users
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('nama_kepala_sekolah', 60);
            $table->string('jabatan', 60);
            $table->string('nip_kepsek', 60);
        });

        Schema::create('wali_kelas', function (Blueprint $table) {
            $table->increments('nip_wali_kelas');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nama_wali_kelas', 60);
        });

        Schema::create('kelas', function (Blueprint $table) {
            $table->tinyIncrements('id_kelas');
            $table->string('nama_kelas', 15);
            $table->string('tingkat_kelas', 5);
            $table->unsignedInteger('nip_wali_kelas');
            $table->unsignedTinyInteger('id_jurusan');
            $table->unsignedTinyInteger('id_angkatan');

            $table->foreign('nip_wali_kelas')->references('nip_wali_kelas')->on('wali_kelas')->onDelete('cascade');
            $table->foreign('id_jurusan')->references('id_jurusan')->on('jurusan')->onDelete('cascade');
            $table->foreign('id_angkatan')->references('id_angkatan')->on('angkatan')->onDelete('cascade');
        });

        Schema::create('siswa', function (Blueprint $table) {
            $table->string('nis', 10)->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedTinyInteger('id_kelas');
            $table->unsignedTinyInteger('id_jurusan');
            $table->string('nama_siswa', 75);
            $table->string('tempat_lahir', 50);
            $table->date('tanggal_lahir');
            $table->string('kontak_siswa', 17);
            
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
            $table->foreign('id_jurusan')->references('id_jurusan')->on('jurusan')->onDelete('cascade');
        });

        Schema::create('pembimbing_sekolah', function (Blueprint $table) {
            $table->increments('nip_pembimbing_sekolah');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nama_pembimbing_sekolah', 60);
            $table->string('kontak_pembimbing_sekolah', 17)->nullable();
            // PENAMBAHAN: Field email untuk pembimbing sekolah
            $table->string('email_pembimbing_sekolah', 100)->nullable();
            // PENGHAPUSAN: Kolom id_perusahaan dihapus
        });

        Schema::create('kompetensi', function (Blueprint $table) {
            $table->tinyIncrements('id_kompetensi');
            $table->unsignedTinyInteger('id_jurusan');
            $table->string('nama_kompetensi', 50);
            
            $table->foreign('id_jurusan')->references('id_jurusan')->on('jurusan')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kompetensi');
        // PENGHAPUSAN: Drop foreign key pembimbing_sekolah dihapus
        Schema::dropIfExists('pembimbing_sekolah');
        Schema::dropIfExists('siswa');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('wali_kelas');
        Schema::dropIfExists('kepala_sekolah');
        Schema::dropIfExists('staff_hubin');
        Schema::table('jurusan', function (Blueprint $table) { if (Schema::hasColumn('jurusan', 'kepala_program')) { $table->dropForeign(['kepala_program']); } });
        Schema::dropIfExists('kepala_program');
        Schema::dropIfExists('guru');
        Schema::dropIfExists('jurusan');
        Schema::dropIfExists('angkatan');
    }
};
