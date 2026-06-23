<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourTec extends Model
{
    protected $table = 'en33_tours_tec';
    public $timestamps = false;

    protected $fillable = ['name', 'lang', 'lang_id', 'icon'];
}
