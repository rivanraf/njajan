<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderDetail;
use App\Models\Table;

class Order extends Model
{
    // WAJIB TAMBAHKAN INI AGAR TOMBOL KUNING BERFUNGSI
    protected $fillable = [
        'customer_name',
        'total_price',
        'payment_status',
        'order_status', 
        'snap_token',
        'payment_type',
        'table_id'
    ];
    
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    // Relationship: Satu Order punya banyak Detail
    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
}
