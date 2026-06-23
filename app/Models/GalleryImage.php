<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $table = 'en33_gallery_images';
    public $timestamps = false;

    protected $fillable = ['image', 'gallery_id'];

    public function gallery()
    {
        return $this->belongsTo(Gallery::class, 'gallery_id');
    }

    public function contents()
    {
        return $this->hasMany(GalleryContent::class, 'image_id');
    }
}
