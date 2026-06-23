<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceSeason extends Model
{
    protected $table = 'en33_services_seasons';
    public $timestamps = false;

    protected $fillable = ['service_id', 'date_from', 'date_to', 'cost'];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
