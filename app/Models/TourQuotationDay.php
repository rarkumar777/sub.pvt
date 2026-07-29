<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourQuotationDay extends Model
{
    protected $table = 'en33_tours_quotation_days';
    public $timestamps = false;

    protected $fillable = [
        'quotation_id', 'day_number', 'expenses', 'total_cost',
        'images', 'contents', 'included', 'excluded',
    ];

    public function quotation()
    {
        return $this->belongsTo(TourQuotation::class, 'quotation_id');
    }
}
