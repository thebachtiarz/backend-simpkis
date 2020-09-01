<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNilaiTambahansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nilai_tambahans', function (Blueprint $table) {
            $table->id();
            // $table->integer('id_semester');
            // $table->integer('id_siswa');
            // $table->integer('id_kegiatan');
            $table->foreignId('id_semester')->constrained('semesters')->cascadeOnDelete();
            $table->foreignId('id_siswa')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('id_kegiatan')->constrained('kegiatans')->cascadeOnDelete();
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
        Schema::dropIfExists('nilai_tambahans');
    }
}
