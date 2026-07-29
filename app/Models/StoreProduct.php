<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreProduct extends Model
{
    protected $table = 'en33_store_products';
    public $timestamps = false;

    protected $fillable = [
        'name', 'title', 'meta_desc', 'meta_key', 'contents', 'published',
        'lang_id', 'lang', 'url', 'price', 'shipping_fee', 'avilable_stock',
        'category_id', 'image',
    ];

    public function images()
    {
        return $this->hasMany(StoreProductImage::class, 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(StoreCategory::class, 'category_id');
    }
}
