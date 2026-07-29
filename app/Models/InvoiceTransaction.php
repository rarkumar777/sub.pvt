<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceTransaction extends Model
{
    protected $table = 'en33_invoices_transactions';
    public $timestamps = false;

    protected $fillable = [
        'description', 'invoice_id', 'total', 'status', 'payment_method',
        'transaction_reference', 'added_by', 'date',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
