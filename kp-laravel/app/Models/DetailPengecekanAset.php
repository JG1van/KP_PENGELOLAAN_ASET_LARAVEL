<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPengecekanAset extends Model
{
    protected $table = 'detail_pengecekan_aset';
    protected $primaryKey = 'Id_Detail_Pengecekan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['Id_Detail_Pengecekan', 'Id_Pengecekan', 'Id_Aset', 'Kondisi'];

    public function pengecekan()
    {
        return $this->belongsTo(PengecekanAset::class, 'Id_Pengecekan');
    }

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'Id_Aset', 'Id_Aset');
    }

}
