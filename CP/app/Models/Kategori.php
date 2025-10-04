<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';
    protected $primaryKey = 'Id_Kategori';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'Id_Kategori',
        'Nama_Kategori'
    ];
}
