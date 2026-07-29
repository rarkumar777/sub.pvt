<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $table = 'en33_tours';
    public $timestamps = false;

    protected $fillable = [
        'nights', 'days', 'status', 'category', 'type', 'rating',
        'f_start', 'f_finish', 'sp_start', 'sp_finish',
        'start_country', 'start_city', 'finish_country', 'finish_city',
        'inclusions', 'map', 'image', 'tec_details', 'relative_count',
        'contact_person', 'pricing_bases', 'pricing_groups', 'min_price', 'max_price',
        'pricing_groups_low', 'pricing_bases_low', 'pricing_groups_high', 'pricing_bases_high',
        'partly_payment', 'itinerary_data', 'pricing_extras',
    ];

    public function customInclusions()
    {
        return $this->hasMany(TourCustomInclusion::class, 'tour_id')->orderBy('sort_order');
    }

    public function contents()
    {
        return $this->hasMany(TourContent::class, 'tour_id');
    }

    public function images()
    {
        return $this->hasMany(TourImage::class, 'tour_id');
    }

    public function bookings()
    {
        return $this->hasMany(TourBooking::class, 'tour_id');
    }

    public function seasons()
    {
        return $this->hasMany(TourSeason::class, 'tour_id');
    }

    public function guaranteedDepartures()
    {
        return $this->hasMany(TourGuaranteedDeparture::class, 'tour_id');
    }

    public function categoryRelation()
    {
        return $this->belongsTo(TourCategory::class, 'category', 'lang_id');
    }

    public function typeRelation()
    {
        return $this->belongsTo(TourType::class, 'type', 'lang_id');
    }

    public function startCountryRelation()
    {
        return $this->belongsTo(Country::class, 'start_country', 'lang_id');
    }

    public function startCityRelation()
    {
        return $this->belongsTo(City::class, 'start_city', 'lang_id');
    }

    public function finishCountryRelation()
    {
        return $this->belongsTo(Country::class, 'finish_country', 'lang_id');
    }

    public function finishCityRelation()
    {
        return $this->belongsTo(City::class, 'finish_city', 'lang_id');
    }

    /**
     * Get content for a specific language.
     */
    public function contentForLang(string $lang)
    {
        return $this->contents()->where('lang', $lang)->first();
    }
}
