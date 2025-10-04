<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailPenempatanTable extends Migration
{
    public function up()
    {
        Schema::create('detail_penempatan_aset', function (Blueprint $table) {
            $table->integer('Id_Detail_Penempatan')->length(2)->primary();
            $table->integer('Id_Penempatan')->collation('utf8mb4_bin');
            $table->integer('Id_Aset');
            $table->integer('Id_Lokasi');
            $table->timestamps();

            $table->foreign('Id_Penempatan')->references('Id_Penempatan')->on('penempatan_aset')->onDelete('cascade');
            $table->foreign('Id_Aset')->references('Id_Aset')->on('aset')->onDelete('cascade');
            $table->foreign('Id_Lokasi')->references('Id_Lokasi')->on('lokasi')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_penempatan_aset');
    }
}
