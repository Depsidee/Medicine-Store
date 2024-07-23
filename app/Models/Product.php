<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable=[
        'tradeName',
        'generic_name_id',
        'category_id',
        'company_id',
        'price',
        'amount',
        'expiringYear',
        'expiringMonth',
        'expiringDay',
        'Show'
    ];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function genericName() {
        return $this->belongsTo(GenericName::class);
    }
}
