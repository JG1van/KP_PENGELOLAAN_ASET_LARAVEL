<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenempatanAset extends Model
{
    protected $table = 'penempatan_aset';
    protected $primaryKey = 'Id_Penempatan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['Id_Penempatan', 'Tanggal_Penempatan', 'user_id'];

    public function detail()
    {
        return $this->hasMany(DetailPenempatan::class, 'Id_Penempatan', 'Id_Penempatan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
