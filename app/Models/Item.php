<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'item_name',
        'qty',
        'price',
        'total',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
