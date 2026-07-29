<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $table = 'en33_galleries';
    public $timestamps = false;

    protected $fillable = ['name', 'lang', 'lang_id'];

    public function images()
    {
        return $this->hasMany(GalleryImage::class, 'gallery_id');
    }
}
