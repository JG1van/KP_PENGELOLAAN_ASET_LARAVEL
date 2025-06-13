<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenempatanAsetTable extends Migration
{
    public function up()
    {
        Schema::create('penempatan_aset', function (Blueprint $table) {
            $table->char('Id_Penempatan', 6)->primary();
            $table->date('Tanggal_Penempatan');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('penempatan_aset');
    }
}
