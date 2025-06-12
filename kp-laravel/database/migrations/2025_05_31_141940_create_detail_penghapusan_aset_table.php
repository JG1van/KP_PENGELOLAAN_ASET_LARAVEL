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
            $table->char('Id_Detail_Penghapusan', 6)->primary();
            $table->char('Id_Penghapusan', 6);
            $table->char('Id_Aset', 5);
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
