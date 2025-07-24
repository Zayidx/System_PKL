<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->increments('id_pengajuan');
            $table->string('nis_siswa', 10);
            // PERBAIKAN: Menyesuaikan semua tipe data foreign key
            $table->unsignedInteger('id_perusahaan');
            $table->unsignedInteger('nip_kepala_program')->nullable();
            $table->unsignedInteger('nip_staff')->nullable();
            $table->string('status_pengajuan', 30);
            $table->string('bukti_penerimaan')->nullable();
            $table->string('token', 64)->nullable();
            // Tambahan field kontrak PKL
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('link_cv', 255)->nullable();
            $table->timestamps();

            $table->foreign('nis_siswa')->references('nis')->on('siswa')->onDelete('cascade');
            $table->foreign('id_perusahaan')->references('id_perusahaan')->on('perusahaan')->onDelete('cascade');
            $table->foreign('nip_kepala_program')->references('nip_kepala_program')->on('kepala_program')->onDelete('cascade');
            $table->foreign('nip_staff')->references('nip_staff')->on('staff_hubin')->onDelete('cascade');
        });

        Schema::create('prakerin', function (Blueprint $table) {
            $table->increments('id_prakerin');
            $table->string('nis_siswa', 10);
            // PERBAIKAN: Menyesuaikan semua tipe data foreign key
            $table->unsignedInteger('nip_pembimbing_sekolah');
            $table->unsignedInteger('id_pembimbing_perusahaan');
            $table->unsignedInteger('id_perusahaan');
            $table->unsignedInteger('nip_kepala_program');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('keterangan')->nullable();

            $table->foreign('nis_siswa')->references('nis')->on('siswa')->onDelete('cascade');
            $table->foreign('nip_pembimbing_sekolah')->references('nip_pembimbing_sekolah')->on('pembimbing_sekolah')->onDelete('cascade');
            $table->foreign('id_pembimbing_perusahaan')->references('id_pembimbing')->on('pembimbing_perusahaan')->onDelete('cascade');
            $table->foreign('id_perusahaan')->references('id_perusahaan')->on('perusahaan')->onDelete('cascade');
            $table->foreign('nip_kepala_program')->references('nip_kepala_program')->on('kepala_program')->onDelete('cascade');
        });

        Schema::create('presensi_siswa', function (Blueprint $table) {
            $table->increments('id_presensi');
            // PERBAIKAN: Menyesuaikan tipe data foreign key
            $table->unsignedInteger('id_pembimbing_perusahaan');
            $table->date('tanggal_kehadiran');
            $table->time('jam_masuk');
            $table->time('jam_pulang');
            $table->string('kegiatan');
            $table->text('keterangan');
            $table->string('status', 15);

            $table->foreign('id_pembimbing_perusahaan')->references('id_pembimbing')->on('pembimbing_perusahaan')->onDelete('cascade');
        });

        Schema::create('monitoring', function (Blueprint $table) {
            $table->increments('id_monitoring');
            // PERBAIKAN: Menyesuaikan tipe data foreign key
            $table->unsignedInteger('id_perusahaan');
            $table->unsignedInteger('nip_pembimbing_sekolah');
            $table->unsignedTinyInteger('id_kepsek'); // id_kepsek tetap tinyInteger
            $table->date('tanggal');
            $table->text('catatan');
            $table->string('verifikasi', 20);

            $table->foreign('id_perusahaan')->references('id_perusahaan')->on('perusahaan')->onDelete('cascade');
            $table->foreign('nip_pembimbing_sekolah')->references('nip_pembimbing_sekolah')->on('pembimbing_sekolah')->onDelete('cascade');
            $table->foreign('id_kepsek')->references('id_kepsek')->on('kepala_sekolah')->onDelete('cascade');
        });

        Schema::create('penilaian', function (Blueprint $table) {
            $table->increments('id_penilaian');
            $table->string('nis_siswa', 10);
            // PERBAIKAN: Menyesuaikan tipe data foreign key
            $table->unsignedInteger('id_pemb_perusahaan');

            $table->foreign('nis_siswa')->references('nis')->on('siswa')->onDelete('cascade');
            $table->foreign('id_pemb_perusahaan')->references('id_pembimbing')->on('pembimbing_perusahaan')->onDelete('cascade');
        });

        Schema::create('nilai', function (Blueprint $table) {
            // PERBAIKAN: Menyesuaikan tipe data foreign key
            $table->unsignedInteger('id_penilaian');
            $table->unsignedTinyInteger('id_kompetensi');
            $table->tinyInteger('nilai');

            $table->foreign('id_penilaian')->references('id_penilaian')->on('penilaian')->onDelete('cascade');
            $table->foreign('id_kompetensi')->references('id_kompetensi')->on('kompetensi')->onDelete('cascade');
            $table->primary(['id_penilaian', 'id_kompetensi']);
        });

        Schema::create('sertifikat', function (Blueprint $table) {
            $table->increments('id_sertifikat');
            // PERBAIKAN: Menyesuaikan tipe data foreign key
            $table->unsignedInteger('id_penilaian');
            $table->string('file_sertifikat');

            $table->foreign('id_penilaian')->references('id_penilaian')->on('penilaian')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sertifikat');
        Schema::dropIfExists('nilai');
        Schema::dropIfExists('penilaian');
        Schema::dropIfExists('monitoring');
        Schema::dropIfExists('presensi_siswa');
        Schema::dropIfExists('prakerin');
        Schema::dropIfExists('pengajuan');
    }
};
