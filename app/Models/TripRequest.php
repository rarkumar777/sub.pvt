<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripRequest extends Model
{
    protected $table = 'trip_requests';

    protected $fillable = [
        'project_stage', 'participant_type', 'adults', 'children', 'children_ages',
        'is_honeymoon', 'group_type',
        'has_exact_dates', 'departure_date', 'return_date', 'flexible_month',
        'departure_period', 'approx_duration',
        'travel_styles', 'accommodation_prefs', 'travel_plan', 'guide_type', 'guide_languages',
        'ideal_budget', 'max_budget', 'currency',
        'civility', 'first_name', 'last_name', 'email', 'phone',
        'password', 'dob', 'country',
        'marketing_consent', 'terms_consent',
        'pipeline_stage', 'assigned_to', 'is_read', 'notes',
    ];

    protected $casts = [
        'children_ages' => 'array',
        'travel_styles' => 'array',
        'accommodation_prefs' => 'array',
        'guide_languages' => 'array',
        'has_exact_dates' => 'boolean',
        'is_honeymoon' => 'boolean',
        'is_read' => 'boolean',
        'marketing_consent' => 'boolean',
        'terms_consent' => 'boolean',
    ];

    public function messages()
    {
        return $this->hasMany(TripRequestMessage::class);
    }

    public function itineraries()
    {
        return $this->hasMany(TripItinerary::class);
    }

    public function latestItinerary()
    {
        return $this->hasOne(TripItinerary::class)->latestOfMany();
    }
}
