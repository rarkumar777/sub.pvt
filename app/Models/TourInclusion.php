<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourInclusion extends Model
{
    protected $table = 'en33_tours_inclusions';
    public $timestamps = false;

    protected $fillable = ['name', 'lang', 'lang_id'];
}
