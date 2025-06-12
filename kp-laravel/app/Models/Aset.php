<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kategori;
use App\Models\DetailPenerimaanAset;
use App\Models\DetailPengecekanAset;
use App\Models\PenerimaanAset;
use App\Models\PenurunanAset;

class Aset extends Model
{
    protected $table = 'aset';
    protected $primaryKey = 'Id_Aset';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'Id_Aset',
        'Nama_Aset',
        'Id_Kategori',
        'STATUS',
        'Nilai_Aset_Awal',
        'Nilai_Saat_Ini',
        'Kondisi',
        'Tanggal_Masuk',
        'Penempatan',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'Id_Kategori', 'Id_Kategori');
    }

    public function detailPenerimaan()
    {
        return $this->hasOne(DetailPenerimaanAset::class, 'Id_Aset', 'Id_Aset');
    }
    public function pengecekanDetails()
    {
        return $this->hasMany(\App\Models\DetailPengecekanAset::class, 'Id_Aset', 'Id_Aset');
    }

    public function penghapusanDetails()
    {
        return $this->hasMany(\App\Models\DetailPenghapusanAset::class, 'Id_Aset', 'Id_Aset');
    }

    public function penerimaan()
    {
        return $this->hasOneThrough(
            PenerimaanAset::class,
            DetailPenerimaanAset::class,
            'Id_Aset',         // Foreign key on DetailPenerimaanAset table
            'Id_Penerimaan',   // Foreign key on PenerimaanAset table
            'Id_Aset',         // Local key on Aset table
            'Id_Penerimaan'    // Local key on DetailPenerimaanAset table
        );
    }

    public function penurunanTerbaru()
    {
        return $this->hasOne(PenurunanAset::class, 'Id_Aset', 'Id_Aset')
            ->orderByDesc('Tahun');
    }

    public function penurunans()
    {
        return $this->hasMany(PenurunanAset::class, 'Id_Aset', 'Id_Aset');
    }

    public function detailPengecekan()
    {
        return $this->hasMany(DetailPengecekanAset::class, 'Id_Aset', 'Id_Aset');
    }
}
