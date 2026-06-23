<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $table = 'en33_slider';
    public $timestamps = false;

    protected $fillable = ['name'];

    public function images()
    {
        return $this->hasMany(SliderImage::class, 'slider_id');
    }
}
