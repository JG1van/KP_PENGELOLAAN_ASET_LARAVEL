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
        Schema::create('aset', function (Blueprint $table) {
            $table->char('Id_Aset', 5)->primary();
            $table->text('Nama_Aset');
            $table->char('Id_Kategori', 2);
            $table->enum('STATUS', ['Aktif', 'Tidak Aktif']);
            $table->decimal('Nilai_Aset_Awal', 12, 2);
            $table->enum('Kondisi', ['Baik', 'Rusak Sedang', 'Rusak Berat', 'Hilang', 'Diremajakan']);


            $table->timestamps();

            $table->foreign('Id_Kategori')->references('Id_Kategori')->on('kategori');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aset');
    }
};
