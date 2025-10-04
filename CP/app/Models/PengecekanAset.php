<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengecekanAset extends Model
{
    protected $table = 'pengecekan_aset';
    protected $primaryKey = 'Id_Pengecekan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['Id_Pengecekan', 'Tanggal_Pengecekan', 'User_Id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'User_Id', 'User_Id');
    }

    public function detail()
    {
        return $this->hasMany(DetailPengecekanAset::class, 'Id_Pengecekan');
    }


}

