<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourCannedDay extends Model
{
    protected $table = 'en33_tours_canned_days';
    public $timestamps = false;

    protected $fillable = ['expenses', 'images', 'included', 'excluded'];

    public function contents()
    {
        return $this->hasMany(TourCannedDayContent::class, 'day_id');
    }
}
