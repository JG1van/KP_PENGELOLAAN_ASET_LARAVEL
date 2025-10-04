<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenghapusanAset extends Model
{
    protected $table = 'penghapusan_aset'; // <- ini penting
    protected $primaryKey = 'Id_Penghapusan';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'Id_Penghapusan',
        'Tanggal_Hapus',
        'Dokumen_Penghapusan',
        'User_Id',
    ];


    public function detail()
    {
        return $this->hasMany(DetailPenghapusanAset::class, 'Id_Penghapusan', 'Id_Penghapusan');
    }
    // Di model PenghapusanAset.php
    public function user()
    {
        return $this->belongsTo(User::class, 'User_Id', 'User_Id');
    }

}
