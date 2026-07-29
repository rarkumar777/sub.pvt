<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreCategory extends Model
{
    protected $table = 'en33_store_categories';
    public $timestamps = false;

    protected $fillable = ['name', 'lang', 'lang_id', 'image'];
}
