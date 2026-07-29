<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreProductImage extends Model
{
    protected $table = 'en33_store_products_images';
    public $timestamps = false;

    protected $fillable = ['product_id', 'image'];

    public function product()
    {
        return $this->belongsTo(StoreProduct::class, 'product_id');
    }
}
