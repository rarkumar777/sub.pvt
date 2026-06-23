<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'en33_invoices';
    public $timestamps = false;

    protected $fillable = [
        'items', 'discount', 'tax', 'status', 'type', 'module', 'user_id',
        'desc', 'date', 'total', 'cost', 'added_by', 'paid_by', 'partly_payment',
        'total_paid', 'due_to_date', 'discount_description', 'sent_count', 'invoices_set',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function expenses()
    {
        return $this->hasMany(InvoiceExpense::class, 'invoice_id');
    }

    public function transactions()
    {
        return $this->hasMany(InvoiceTransaction::class, 'invoice_id');
    }

    public function expensesIdentifiers()
    {
        return $this->hasMany(ExpensesIdentifier::class, 'invoice_id');
    }

    public function booking()
    {
        return $this->hasOne(TourBooking::class, 'invoice_id');
    }

    public function departureBooking()
    {
        return $this->hasOne(TourDepartureBooking::class, 'invoice_id');
    }
}
