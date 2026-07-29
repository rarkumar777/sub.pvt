<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'en33_currency';
    public $timestamps = false;

    protected $fillable = ['name', 'lang', 'lang_id', 'symbol', 'rate'];
}
