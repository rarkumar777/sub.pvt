<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourTraveler extends Model
{
    protected $table = 'en33_tours_travelers_list';
    public $timestamps = false;

    protected $fillable = [
        'booking_id', 'guaranteed_booking_id', 'name', 'passport_number',
        'passport_issue', 'passport_expire', 'birth_date', 'nationality',
        'flight_number', 'border', 'traveler_id', 'room_id',
    ];

    public function booking()
    {
        return $this->belongsTo(TourBooking::class, 'booking_id');
    }
}
