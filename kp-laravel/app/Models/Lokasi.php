<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $table = 'lokasi';
    protected $primaryKey = 'Id_Lokasi';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['Id_Lokasi', 'Nama_Lokasi'];
    public function lokasi()
    {
        return $this->belongsTo(\App\Models\Lokasi::class, 'Id_Lokasi', 'Id_Lokasi');
    }

}
