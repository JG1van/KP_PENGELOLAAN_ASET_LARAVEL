<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailPenempatanTable extends Migration
{
    public function up()
    {
        Schema::create('detail_penempatan_aset', function (Blueprint $table) {
            $table->char('Id_Detail_Penempatan', 6)->primary();
            $table->char('Id_Penempatan', 6);
            $table->char('Id_Aset', 5);
            $table->char('Id_Lokasi', 3);
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
