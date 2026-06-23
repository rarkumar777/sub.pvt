<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SliderImage extends Model
{
    protected $table = 'en33_slider_images';
    public $timestamps = false;

    protected $fillable = ['image', 'slider_id', 'price'];

    public function slider()
    {
        return $this->belongsTo(Slider::class, 'slider_id');
    }

    public function contents()
    {
        return $this->hasMany(SliderContent::class, 'image_id');
    }
}
