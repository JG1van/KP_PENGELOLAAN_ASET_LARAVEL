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
        Schema::create('pengecekan_aset', function (Blueprint $table) {
            $table->integer('Id_Pengecekan')->length(2)->primary();
            $table->date('Tanggal_Pengecekan');
            $table->integer('User_Id');
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
        Schema::dropIfExists('pengecekan_aset');
    }
};
