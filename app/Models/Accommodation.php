<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accommodation extends Model
{
    protected $table = 'en33_accommodations';
    public $timestamps = true;

    protected $fillable = ['descriptionL', 'image', 'notes', 'acc_type', 'acc_category', 'website', 'arrival', 'transport_method', 'departure_location', 'arrival_destination', 'length_time', 'distance_km', 'cost', 'vender', 'category', 'country', 'restricted'];

    // Accessor: $service->description returns descriptionL column
    public function getDescriptionAttribute()
    {
        return $this->attributes['descriptionL'] ?? null;
    }

    // Mutator: $service->description = 'x' sets descriptionL column
    public function setDescriptionAttribute($value)
    {
        $this->attributes['descriptionL'] = $value;
    }

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class, 'category');
    }

    public function venderUser()
    {
        return $this->belongsTo(User::class, 'vender');
    }
}
