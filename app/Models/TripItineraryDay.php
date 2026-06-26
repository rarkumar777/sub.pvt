<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripItineraryDay extends Model
{
    protected $fillable = [
        'trip_itinerary_id', 'day_number', 'duration', 'title', 'description',
        'destinations', 'breakfast', 'lunch', 'dinner',
        'accommodation_name', 'accommodation_description',
        'accommodation_category', 'accommodation_stars', 'accommodation_website', 'photos', 'services', 'canned_day_id',
    ];

    protected $casts = [
        'breakfast' => 'boolean',
        'lunch' => 'boolean',
        'dinner' => 'boolean',
        'photos' => 'array',
        'services' => 'array',
    ];

    public function itinerary()
    {
        return $this->belongsTo(TripItinerary::class, 'trip_itinerary_id');
    }
}
