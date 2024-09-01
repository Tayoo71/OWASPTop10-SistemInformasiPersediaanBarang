<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    use HasFactory;
    protected $table = 'jenises';
    protected $fillable = ['nama_jenis', 'keterangan'];
    public $timestamps = false;
    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }
    public function scopeSearch($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('id', 'like', '%' . $search . '%')
                ->orWhere('nama_jenis', 'like', '%' . $search . '%')
                ->orWhere('keterangan', 'like', '%' . $search . '%');
        });
    }
}
