<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'menu_id',
        'qty',
        'subtotal', // sesuaikan dengan nama di database kamu (subtotal)
        'variant',  // WAJIB ADA
        'notes',    // WAJIB ADA
    ];

    // Relationship: Satu Detail merujuk ke satu Menu
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
