<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * fillable: Daftar kolom yang diizinkan untuk diisi secara massal.
     */
    protected $fillable = [
        'category_id', 
        'name', 
        'type',        
        'description', 
        'price', 
        'image', 
        'is_available',
        'status_stok'
    ];

    /**
     * Relasi ke model Category.
     * Satu menu dimiliki oleh satu kategori.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke model OrderDetail (Opsional, tapi berguna untuk laporan).
     */
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}