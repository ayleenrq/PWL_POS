<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserModel;
use App\Models\PenjualanDetailModel;

class PenjualanModel extends Model
{
    use HasFactory;

    protected $table = 't_penjualan'; // mendefinisikan nama tabel yang digunakan oleh model ini
    protected $primaryKey = 'penjualan_id'; // mendefinisikan primary key
    /**
     * Summary of fillable
     * @var array
     */
    protected $fillable = [
        'user_id',
        'penjualan_kode',
        'pembeli',
        'penjualan_tanggal',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
    public function penjualan_detail()
    {
        return $this->hasMany(PenjualanDetailModel::class, 'penjualan_id', 'penjualan_id');
    }
    
    
}