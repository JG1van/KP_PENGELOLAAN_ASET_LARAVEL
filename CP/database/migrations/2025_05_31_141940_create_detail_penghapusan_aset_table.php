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
        Schema::create('detail_penghapusan_aset', function (Blueprint $table) {
            $table->integer('Id_Detail_Penghapusan')->length(4)->primary();
            $table->integer('Id_Penghapusan');
            $table->integer('Id_Aset');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_penghapusan_aset');
    }
};
