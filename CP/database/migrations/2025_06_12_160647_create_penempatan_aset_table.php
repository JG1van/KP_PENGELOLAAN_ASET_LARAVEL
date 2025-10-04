<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenempatanAsetTable extends Migration
{
    public function up()
    {
        Schema::create('penempatan_aset', function (Blueprint $table) {
            $table->integer('Id_Penempatan')->length(2)->primary();
            $table->date('Tanggal_Penempatan');
            $table->integer('User_Id');
            $table->timestamps();

            $table->foreign('User_Id')->references('User_Id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('penempatan_aset');
    }
}
