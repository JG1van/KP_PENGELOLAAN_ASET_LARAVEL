<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenempatan extends Model
{
    protected $table = 'detail_penempatan_aset';
    protected $primaryKey = 'Id_Detail_Penempatan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['Id_Detail_Penempatan', 'Id_Penempatan', 'Id_Aset', 'Id_Lokasi'];

    public function penempatan()
    {
        return $this->belongsTo(PenempatanAset::class, 'Id_Penempatan', 'Id_Penempatan');
    }

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'Id_Aset', 'Id_Aset');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'Id_Lokasi', 'Id_Lokasi');
    }
    public function penempatanDetail()
    {
        return $this->hasMany(\App\Models\DetailPenempatan::class, 'Id_Aset', 'Id_Aset');
    }

}
