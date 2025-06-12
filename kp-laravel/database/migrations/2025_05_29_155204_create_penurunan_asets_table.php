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
    public function up(): void
    {
        Schema::create('penurunan_aset', function (Blueprint $table) {
            $table->char('Id_Penurunan', 4)->primary();
            $table->year('Tahun');
            $table->char('Id_Aset', 5);
            $table->decimal('Nilai_Saat_Ini', 12, 2);
            $table->timestamps();

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
        Schema::dropIfExists('penurunan_aset');
    }
};
