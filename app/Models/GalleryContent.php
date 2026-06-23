<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryContent extends Model
{
    protected $table = 'en33_gallery_contents';
    public $timestamps = false;

    protected $fillable = ['image_id', 'lang', 'text'];

    public function image()
    {
        return $this->belongsTo(GalleryImage::class, 'image_id');
    }
}
