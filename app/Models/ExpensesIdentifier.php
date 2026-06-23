<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpensesIdentifier extends Model
{
    protected $table = 'en33_expenses_identifier';
    public $timestamps = false;

    protected $fillable = ['code', 'invoice_id', 'vender_id'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
