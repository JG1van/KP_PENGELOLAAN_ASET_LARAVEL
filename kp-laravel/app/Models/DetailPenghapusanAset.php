<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenghapusanAset extends Model
{
    protected $table = 'detail_penghapusan_aset';
    protected $primaryKey = 'Id_Detail_Penghapusan';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'Id_Detail_Penghapusan',
        'Id_Penghapusan',
        'Id_Aset',
    ];
    public function aset()
    {
        return $this->belongsTo(\App\Models\Aset::class, 'Id_Aset', 'Id_Aset');
    }

}
