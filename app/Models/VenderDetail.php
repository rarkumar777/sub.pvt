<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenderDetail extends Model
{
    protected $table = 'en33_vender_details';
    public $timestamps = false;
    protected $primaryKey = 'vender_id';
    public $incrementing = false;

    protected $fillable = ['vender_id', 'description', 'images'];

    public function user()
    {
        return $this->belongsTo(User::class, 'vender_id');
    }
}
