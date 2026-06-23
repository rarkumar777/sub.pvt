<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'en33_cities';
    public $timestamps = false;

    protected $fillable = ['name', 'lang', 'lang_id', 'country'];

    public function countryRelation()
    {
        return $this->belongsTo(Country::class, 'country', 'lang_id');
    }
}
