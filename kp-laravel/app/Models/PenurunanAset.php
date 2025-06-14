<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenurunanAset extends Model
{
    protected $table = 'penurunan_aset';
    protected $primaryKey = 'Id_Penurunan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'Id_Penurunan',
        'Id_Aset',
        'Tahun',
        'Nilai_Saat_Ini',
    ];

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'Id_Aset', 'Id_Aset');
    }
}
