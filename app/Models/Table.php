<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara massal.
     */
    protected $fillable = [
        'number',
        'hash',
        'status',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'table_id');
    }
}