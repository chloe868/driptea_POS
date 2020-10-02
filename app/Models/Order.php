<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'productId', 'quantity', 'size', 'cupType', 'sugarLevel', 'subTotal', 'status'
    ];

    public function orderProduct(){
        return $this->hasMany('App\Models\Product','id','productId');
    }

    public function sameOrder(){
        return $this->hasMany('App\Models\AddOns','orderId','id');
    }
}