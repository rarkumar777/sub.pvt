<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourDepartureBooking extends Model
{
    protected $table = 'en33_tours_departure_booking';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'adult', 'child', 'invoice_id', 'guaranteed_departure_id',
        'single', 'double', 'twin', 'triple', 'quad', 'counted_persons',
        'note', 'hotel_grade', 'trip_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function guaranteedDeparture()
    {
        return $this->belongsTo(TourGuaranteedDeparture::class, 'guaranteed_departure_id');
    }
}
