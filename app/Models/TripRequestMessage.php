<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripRequestMessage extends Model
{
    protected $fillable = [
        'trip_request_id', 'user_id', 'sender_type', 'sender_name', 'message', 'attachment',
    ];

    public function tripRequest()
    {
        return $this->belongsTo(TripRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
