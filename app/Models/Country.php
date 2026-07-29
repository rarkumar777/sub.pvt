<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'en33_countries';
    public $timestamps = false;

    protected $fillable = ['name', 'lang', 'lang_id'];

    public function cities()
    {
        return $this->hasMany(City::class, 'country', 'lang_id');
    }
}
