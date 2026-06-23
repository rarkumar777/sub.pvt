<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourQuotationPricing extends Model
{
    protected $table = 'en33_tours_quotation_pricing';
    public $timestamps = false;

    protected $fillable = ['description', 'customer_type', 'min_profit', 'type', 'value', 'commission'];

    public function quotations()
    {
        return $this->hasMany(TourQuotation::class, 'pricing_base');
    }
}
