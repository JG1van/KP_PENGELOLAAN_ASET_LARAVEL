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
        Schema::create('detail_pengecekan_aset', function (Blueprint $table) {
            $table->char('Id_Detail_Pengecekan', 6)->primary();
            $table->char('Id_Pengecekan', 6);
            $table->char('Id_Aset', 5);
            $table->enum('Kondisi', ['Baik', 'Rusak Sedang', 'Rusak Berat', 'Hilang', 'Diremajakan']);
            $table->timestamps();

            // Pastikan tabel referensi dan kolom cocok
            $table->foreign('Id_Pengecekan')->references('Id_Pengecekan')->on('pengecekan_aset')->onDelete('cascade');
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
        Schema::dropIfExists('detail_pengecekan_aset');
    }
};
