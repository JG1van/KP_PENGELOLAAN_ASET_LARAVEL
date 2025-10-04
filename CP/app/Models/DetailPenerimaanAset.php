<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PenerimaanAset;
use App\Models\Aset;

class DetailPenerimaanAset extends Model
{
    use HasFactory;

    protected $table = 'detail_penerimaan_aset';
    protected $primaryKey = 'Id_Detail_Penerimaan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'Id_Detail_Penerimaan',
        'Id_Penerimaan',
        'Id_Aset'
    ];

    public function penerimaan()
    {
        return $this->belongsTo(PenerimaanAset::class, 'Id_Penerimaan', 'Id_Penerimaan');
    }

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'Id_Aset', 'Id_Aset');
    }
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'Id_Lokasi', 'Id_Lokasi');
    }

}
