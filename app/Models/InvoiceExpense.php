<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceExpense extends Model
{
    protected $table = 'en33_invoices_expenses';
    public $timestamps = false;

    protected $fillable = [
        'invoice_id', 'cost', 'added_by', 'time', 'service_date', 'service_time',
        'service_end_date', 'status', 'payment_status', 'service_id',
        'confirmation_number', 'qty', 'vender_notify', 'paid_by', 'remarks', 'desc',
        'vender', 'duration', 'cost_per_unit',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function venderUser()
    {
        return $this->belongsTo(User::class, 'vender');
    }

    public function addedByUser()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function booking()
    {
        return $this->hasOne(TourBooking::class, 'invoice_id', 'invoice_id');
    }
}
