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
        Schema::create('penerimaan_aset', function (Blueprint $table) {
            $table->integer('Id_Penerimaan')->length(2)->primary();
            $table->date('Tanggal_Terima');
            $table->text('Keterangan');
            $table->string('Dokumen_Penerimaan', 255);
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
        Schema::dropIfExists('penerimaan_aset');
    }
};
