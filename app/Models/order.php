<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'totalPrice',
        'year',
        'month',
        'day',
        'deliveryStatus',
        'paymentStatus'
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }

}
