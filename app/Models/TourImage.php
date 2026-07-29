<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourImage extends Model
{
    protected $table = 'en33_tours_images';
    public $timestamps = false;

    protected $fillable = ['tour_id', 'image'];

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }
}
