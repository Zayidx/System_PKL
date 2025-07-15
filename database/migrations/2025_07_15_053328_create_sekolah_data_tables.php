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
            $table->string('nama_jurusan_singkat', 5);
        });

        Schema::create('guru', function (Blueprint $table) {
            $table->tinyIncrements('nip_guru');
            $table->string('nama_guru', 60);
        });

        Schema::create('kepala_program', function (Blueprint $table) {
            $table->tinyIncrements('nip_kepala_program');
            $table->unsignedTinyInteger('nip_guru');
            $table->unsignedTinyInteger('id_jurusan');
            $table->string('nama_kepala_program', 60);

            $table->foreign('nip_guru')->references('nip_guru')->on('guru')->onDelete('cascade');
            $table->foreign('id_jurusan')->references('id_jurusan')->on('jurusan')->onDelete('cascade');
        });

        Schema::table('jurusan', function (Blueprint $table) {
            $table->unsignedTinyInteger('kepala_program')->nullable();
            $table->foreign('kepala_program')->references('nip_kepala_program')->on('kepala_program')->onDelete('set null');
        });

        Schema::create('staff_hubin', function (Blueprint $table) {
            $table->tinyIncrements('nip_staff');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_staff', 60);
        });

        Schema::create('kepala_sekolah', function (Blueprint $table) {
            $table->tinyIncrements('id_kepsek');
            $table->string('nama_kepala_sekolah', 60);
            $table->string('jabatan', 60);
            $table->string('nip_kepsek', 60);
        });

        Schema::create('kontak_guru', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('id_guru');
            $table->string('kontak', 17);

            $table->foreign('id_guru')->references('nip_guru')->on('guru')->onDelete('cascade');
        });

        Schema::create('wali_kelas', function (Blueprint $table) {
            $table->tinyIncrements('nip_wali_kelas');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nama_wali_kelas', 60);
        });

        Schema::create('kelas', function (Blueprint $table) {
            $table->tinyIncrements('id_kelas');
            $table->string('nama_kelas', 5);
            $table->string('tingkat_kelas', 5);
            $table->unsignedTinyInteger('nip_wali_kelas');
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

            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
            $table->foreign('id_jurusan')->references('id_jurusan')->on('jurusan')->onDelete('cascade');
        });

        Schema::create('kontak_siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nis_siswa', 10);
            $table->string('kontak', 17);
            
            $table->foreign('nis_siswa')->references('nis')->on('siswa')->onDelete('cascade');
        });

        Schema::create('pembimbing_sekolah', function (Blueprint $table) {
            $table->tinyIncrements('nip_pembimbing_sekolah');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nama_pembimbing_sekolah', 60);
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
        Schema::dropIfExists('pembimbing_sekolah');
        Schema::dropIfExists('kontak_siswa');
        Schema::dropIfExists('siswa');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('wali_kelas');
        Schema::dropIfExists('kontak_guru');
        Schema::dropIfExists('kepala_sekolah');
        Schema::dropIfExists('staff_hubin');
        Schema::table('jurusan', function (Blueprint $table) {
            if (Schema::hasColumn('jurusan', 'kepala_program')) {
                $table->dropForeign(['kepala_program']);
            }
        });
        Schema::dropIfExists('kepala_program');
        Schema::dropIfExists('guru');
        Schema::dropIfExists('jurusan');
        Schema::dropIfExists('angkatan');
    }
};
