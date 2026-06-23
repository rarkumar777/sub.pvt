<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourSeason extends Model
{
    protected $table = 'en33_tours_seasons';
    public $timestamps = false;

    protected $fillable = ['tour_id', 'from_date', 'to_date', 'type'];

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }
}
