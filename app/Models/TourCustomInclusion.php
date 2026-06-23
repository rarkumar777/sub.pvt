<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourCustomInclusion extends Model
{
    protected $table = 'tour_custom_inclusions';
    public $timestamps = false;

    protected $fillable = ['tour_id', 'name', 'type', 'sort_order'];

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }
}
