<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'review';
    protected $fillable = ['product_id', 'user_id', 'rating', 'review'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_code');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
