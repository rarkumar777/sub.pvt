<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourGuaranteedDeparture extends Model
{
    protected $table = 'en33_tours_guaranteed_departure';
    public $timestamps = false;

    protected $fillable = [
        'tour_id', 'date', 'min_to_operate', 'max_to_operate',
        'adult_price', 'early_bird_price', 'last_minute_price',
        'child_price', 'child_early_bird_price', 'child_last_minute_price',
        'booked_pending', 'booked_paid',
        'early_bird_from_date', 'early_bird_to_date',
        'last_minute_from_date', 'last_minute_to_date',
        'booking_id', 'hotel_grade',
        '2_star_supplements', '3_star_supplements', '4_star_supplements', '5_star_supplements',
        '1_single_supplement', '2_single_supplement', '3_single_supplement', '4_single_supplement', '5_single_supplement',
        'title', 'status',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }

    public function departureBookings()
    {
        return $this->hasMany(TourDepartureBooking::class, 'guaranteed_departure_id');
    }

    public function booking()
    {
        return $this->belongsTo(TourBooking::class, 'booking_id');
    }
}
