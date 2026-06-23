<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class TourCannedDayContent extends Model
{
    protected $table = 'en33_tours_canned_days_contents';
    public $timestamps = false;

    protected $fillable = ['day_id', 'lang', 'title', 'place', 'description'];

    /**
     * Decode HTML entities in title
     */
    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? html_entity_decode($value, ENT_QUOTES, 'UTF-8') : $value,
        );
    }

    public function cannedDay()
    {
        return $this->belongsTo(TourCannedDay::class, 'day_id');
    }
}
