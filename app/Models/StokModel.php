<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\BarangModel;
use App\Models\SupplierModel;
use App\Models\UserModel;

class StokModel extends Model
{
    use HasFactory;
    protected $table = "t_stok";
    protected $primaryKey = "stok_id";
    protected $fillable = ["stok_id", "tanggal", "barang_id", "jenis_stok", "supplier_id", "user_id", "stok_jumlah"];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(BarangModel::class, 'barang_id', 'barang_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(SupplierModel::class, 'supplier_id', 'supplier_id');
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\UserModel::class, 'user_id', 'user_id');
    }
}
