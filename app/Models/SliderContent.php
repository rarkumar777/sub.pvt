<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SliderContent extends Model
{
    protected $table = 'en33_slider_contents';
    public $timestamps = false;

    protected $fillable = ['image_id', 'lang', 'text', 'text2', 'link'];

    public function image()
    {
        return $this->belongsTo(SliderImage::class, 'image_id');
    }
}
