<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'en33_services';
    public $timestamps = false;

    protected $fillable = ['description', 'image', 'notes', 'acc_type', 'acc_category', 'website', 'arrival', 'transport_method', 'departure_location', 'arrival_destination', 'length_time', 'distance_km', 'cost', 'vender', 'category', 'country', 'restricted'];

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class, 'category');
    }

    public function venderUser()
    {
        return $this->belongsTo(User::class, 'vender');
    }

    public function seasons()
    {
        return $this->hasMany(ServiceSeason::class, 'service_id');
    }
}
