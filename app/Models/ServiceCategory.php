<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $table = 'en33_services_categories';
    public $timestamps = false;

    protected $fillable = ['name', 'parent_id', 'country_id'];

    public function parent()
    {
        return $this->belongsTo(ServiceCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ServiceCategory::class, 'parent_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'category');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'lang_id');
    }
}
