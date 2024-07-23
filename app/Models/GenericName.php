<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenericName extends Model
{
    use HasFactory;
    protected $fillable=[
        'name'
    ];

    public function products() {
        return $this->hasMany(Product::class)->with('category','company','genericName');
    }
}
