<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id', 'amount', 'method', 'status',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

}