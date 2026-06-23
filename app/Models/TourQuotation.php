<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourQuotation extends Model
{
    protected $table = 'en33_tours_quotation';
    public $timestamps = false;

    protected $fillable = [
        'customer_name', 'email', 'phone', 'ref_number', 'travel_date',
        'days', 'nights', 'pricing_base', 'description', 'travelers_number',
        'lang', 'added_by', 'last_edited', 'views', 'total_cost', 'total',
        'invoice_id', 'status', 'profit_amount',
    ];

    public function quotationDays()
    {
        return $this->hasMany(TourQuotationDay::class, 'quotation_id');
    }

    public function pricingBase()
    {
        return $this->belongsTo(TourQuotationPricing::class, 'pricing_base');
    }

    public function addedByUser()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function bookings()
    {
        return $this->hasMany(TourBooking::class, 'quotation_id');
    }
}
