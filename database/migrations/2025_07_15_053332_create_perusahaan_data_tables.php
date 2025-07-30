<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('perusahaan', function (Blueprint $table) {
            // PERBAIKAN: Menggunakan increments() agar konsisten.
            $table->increments('id_perusahaan');
            $table->string('nama_perusahaan', 100);
            $table->text('alamat_perusahaan');
            $table->string('email_perusahaan', 100)->nullable();
            // PENAMBAHAN: Kolom kontak yang hilang
            $table->string('kontak_perusahaan', 20)->nullable();
            $table->string('logo_perusahaan')->nullable();
            // PENAMBAHAN: Kolom pembimbing
            $table->unsignedInteger('nip_pembimbing_sekolah')->nullable();
            $table->unsignedInteger('id_pembimbing_perusahaan')->nullable();
        });

        Schema::create('pembimbing_perusahaan', function (Blueprint $table) {
            // PERBAIKAN: Menggunakan increments() agar konsisten.
            $table->increments('id_pembimbing');
            // PERBAIKAN: Tipe data disesuaikan dengan primary key di 'perusahaan'.
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->unsignedInteger('id_perusahaan');
            $table->string('nama', 75);
            $table->string('no_hp', 17);
            // PENAMBAHAN: Field email untuk pembimbing perusahaan
            $table->string('email', 100)->nullable();

            $table->foreign('id_perusahaan')->references('id_perusahaan')->on('perusahaan')->onDelete('cascade');
        });

        Schema::create('kontak_perusahaan', function (Blueprint $table) {
            $table->increments('id_kontak');
            // PERBAIKAN: Tipe data disesuaikan dengan primary key di 'perusahaan'.
            $table->unsignedInteger('id_perusahaan');
            $table->string('kontak_perusahaan', 17);

            $table->foreign('id_perusahaan')->references('id_perusahaan')->on('perusahaan')->onDelete('cascade');
        });

        Schema::table('siswa', function (Blueprint $table) {
            // PERBAIKAN: Tipe data disesuaikan dengan primary key di 'perusahaan'.
            $table->unsignedInteger('id_perusahaan')->nullable();
            $table->foreign('id_perusahaan')->references('id_perusahaan')->on('perusahaan')->onDelete('set null');
        });

        // PENAMBAHAN: Foreign key untuk pembimbing
        Schema::table('perusahaan', function (Blueprint $table) {
            $table->foreign('nip_pembimbing_sekolah')->references('nip_pembimbing_sekolah')->on('pembimbing_sekolah')->onDelete('set null');
            $table->foreign('id_pembimbing_perusahaan')->references('id_pembimbing')->on('pembimbing_perusahaan')->onDelete('set null');
        });

        // PENGHAPUSAN: Foreign key pembimbing_sekolah dihapus
    }

    public function down()
    {
        Schema::table('siswa', function (Blueprint $table) {
            if (Schema::hasColumn('siswa', 'id_perusahaan')) {
                $table->dropForeign(['id_perusahaan']);
                $table->dropColumn('id_perusahaan');
            }
        });
        
        // PENAMBAHAN: Drop foreign key untuk pembimbing
        Schema::table('perusahaan', function (Blueprint $table) {
            if (Schema::hasColumn('perusahaan', 'nip_pembimbing_sekolah')) {
                $table->dropForeign(['nip_pembimbing_sekolah']);
                $table->dropColumn('nip_pembimbing_sekolah');
            }
            if (Schema::hasColumn('perusahaan', 'id_pembimbing_perusahaan')) {
                $table->dropForeign(['id_pembimbing_perusahaan']);
                $table->dropColumn('id_pembimbing_perusahaan');
            }
        });
        
        // PENGHAPUSAN: Drop foreign key pembimbing_sekolah dihapus
        
        Schema::dropIfExists('kontak_perusahaan');
        Schema::dropIfExists('pembimbing_perusahaan');
        Schema::dropIfExists('perusahaan');
    }
};
