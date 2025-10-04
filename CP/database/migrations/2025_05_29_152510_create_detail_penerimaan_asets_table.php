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
        Schema::create('detail_penerimaan_aset', function (Blueprint $table) {
            $table->integer('Id_Detail_Penerimaan')->primary();
            $table->integer('Id_Penerimaan')->collation('utf8mb4_bin');
            $table->integer('Id_Aset');
            $table->timestamps();
            $table->foreign('Id_Penerimaan')->references('Id_Penerimaan')->on('penerimaan_aset')->onDelete('cascade');
            $table->foreign('Id_Aset')->references('Id_Aset')->on('aset')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_penerimaan_aset');
    }
};
