<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripItinerary extends Model
{
    protected $fillable = [
        'trip_request_id', 'title', 'traveler_surname', 'language',
        'arrival_date', 'cover_photo', 'video_url', 'price_per_person', 'num_travelers',
        'group_total', 'price_includes', 'price_excludes', 'booking_conditions', 'status',
        'payment_conditions', 'reduced_mobility', 'passports_visas', 'travel_insurance',
        'nights_included', 'agency_commission', 'commission_type',
    ];

    protected $casts = [
        'arrival_date' => 'date',
        'price_per_person' => 'decimal:2',
        'group_total' => 'decimal:2',
    ];

    public function tripRequest()
    {
        return $this->belongsTo(TripRequest::class);
    }

    public function days()
    {
        return $this->hasMany(TripItineraryDay::class)->orderBy('day_number');
    }
}
