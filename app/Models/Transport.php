<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    protected $table = 'en33_transports';
    public $timestamps = true;

    protected $fillable = ['description', 'image', 'notes', 'acc_type', 'acc_category', 'website', 'arrival', 'transport_method', 'departure_location', 'arrival_destination', 'length_time', 'distance_km', 'cost', 'vender', 'category', 'country', 'restricted'];

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class, 'category');
    }

    public function venderUser()
    {
        return $this->belongsTo(User::class, 'vender');
    }
}
