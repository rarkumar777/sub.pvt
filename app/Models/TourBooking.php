<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourBooking extends Model
{
    protected $table = 'en33_tours_booking';
    public $timestamps = false;

    protected $fillable = [
        'nights', 'days', 'user_id', 'travel_date', 'booked_in_date', 'tour_id',
        'invoice_id', 'hotel_grade', 'room_single', 'rooms_double', 'rooms_twin',
        'rooms_triple', 'rooms_quad', 'note', 'start_country', 'added_by',
        'adult', 'child', 'infant', 'paid_by', 'trip_status',
        'guaranteed_departure_id', 'invoices', 'guest_name', 'booking_itinerary',
        'quotation_id',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function travelers()
    {
        return $this->hasMany(TourTraveler::class, 'booking_id');
    }

    public function guaranteedDeparture()
    {
        return $this->belongsTo(TourGuaranteedDeparture::class, 'guaranteed_departure_id');
    }

    public function quotation()
    {
        return $this->belongsTo(TourQuotation::class, 'quotation_id');
    }
}
