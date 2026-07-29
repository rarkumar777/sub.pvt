<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourType extends Model
{
    protected $table = 'en33_tours_types';
    public $timestamps = false;

    protected $fillable = ['name', 'lang', 'lang_id'];
}
