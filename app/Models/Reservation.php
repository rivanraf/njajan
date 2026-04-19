<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    // Menentukan kolom mana saja yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'booking_code',
        'name',
        'whatsapp',
        'table_id',
        'reservation_date',
        'reservation_time',
        'guests',
        'status',
    ];

    /**
     * Relasi ke Model Table (Jika kamu punya model Table)
     * Ini berguna agar kita bisa tahu reservasi ini untuk meja nomor berapa.
     */
    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}