<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Sfolador\Devices\Models\Concerns\HasDevices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\medicine;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable ;
    protected $table="users";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'phoneNumber',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function favoriteProducts(){
        return $this->belongsToMany(product::class,'product_user');
    }
    public function myOrders(){
        return $this->hasMany(order::class,'user_id');
    }
}
