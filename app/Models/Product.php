<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'product_code',
        'product_name',
        'product_category',
        'product_quantity',
        'product_desc',
        'product_price',
        'product_img_main',
        'product_img_1',
        'product_img_2',
        'product_img_3',
        'product_delivery_time',
    ];
}
