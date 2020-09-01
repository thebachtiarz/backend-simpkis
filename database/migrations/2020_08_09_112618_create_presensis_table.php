<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresensisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            // $table->integer('id_presensi');
            // $table->integer('id_semester');
            // $table->integer('id_kegiatan');
            // $table->integer('id_siswa');
            $table->foreignId('id_presensi')->constrained('presensi_groups')->cascadeOnDelete();
            $table->foreignId('id_semester')->constrained('semesters')->cascadeOnDelete();
            $table->foreignId('id_kegiatan')->constrained('kegiatans')->cascadeOnDelete();
            $table->foreignId('id_siswa')->constrained('siswas')->cascadeOnDelete();
            $table->string('nilai');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('presensis');
    }
}
