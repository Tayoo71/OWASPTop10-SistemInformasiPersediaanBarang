<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    use HasFactory;
    protected $table = 'jenises';
    protected $fillable = ['nama_jenis'];
    public $timestamps = false;
    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }
}
