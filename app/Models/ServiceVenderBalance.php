<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceVenderBalance extends Model
{
    protected $table = 'en33_services_vender_balance';
    public $timestamps = false;

    protected $fillable = ['vender_id', 'balance'];

    public function vender()
    {
        return $this->belongsTo(User::class, 'vender_id');
    }
}
