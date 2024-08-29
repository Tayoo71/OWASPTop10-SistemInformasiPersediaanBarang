<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merek extends Model
{
    use HasFactory;
    protected $fillable = ['nama_merek'];
    public $timestamps = false;
    public function barangs()
    {
        return $this->hasMany(Barang::class, 'merek_id', 'id');
    }
}
