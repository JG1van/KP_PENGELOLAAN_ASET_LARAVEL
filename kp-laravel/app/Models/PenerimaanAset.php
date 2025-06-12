<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenerimaanAset extends Model
{
    protected $table = 'penerimaan_aset';
    protected $primaryKey = 'Id_Penerimaan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'Id_Penerimaan',
        'Tanggal_Terima',
        'Keterangan',
        'Dokumen_Penerimaan',
        'User_Id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailPenerimaan()
    {
        return $this->hasMany(DetailPenerimaanAset::class, 'Id_Penerimaan', 'Id_Penerimaan');

    }
    public function detail()
    {
        return $this->hasMany(\App\Models\DetailPenerimaanAset::class, 'Id_Penerimaan', 'Id_Penerimaan');
    }


}
