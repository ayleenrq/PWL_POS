<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\BarangModel;
use App\Models\RiwayatStokModel;
use App\Models\UserModel;

class StokModel extends Model
{
    use HasFactory;
    
    protected $table = "t_stok";
    protected $primaryKey = "stok_id";
    protected $fillable = ["stok_id", "barang_id", "stok_jumlah"];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(BarangModel::class, 'barang_id', 'barang_id');
    }
}
