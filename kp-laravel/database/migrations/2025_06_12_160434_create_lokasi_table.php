<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLokasiTable extends Migration
{
    public function up()
    {
        Schema::create('lokasi', function (Blueprint $table) {
            $table->char('Id_Lokasi', 3)->primary();
            $table->string('Nama_Lokasi', 100);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lokasi');
    }
}
