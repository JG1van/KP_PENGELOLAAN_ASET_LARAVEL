<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penghapusan_aset', function (Blueprint $table) {
            $table->integer('Id_Penghapusan')->length(2)->primary();
            $table->date('Tanggal_Hapus');
            $table->string('Dokumen_Penghapusan', 255);
            $table->Integer('User_Id');
            $table->timestamps();

            $table->foreign('User_Id')->references('User_Id')->on('users')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penghapusan_aset');
    }
};
