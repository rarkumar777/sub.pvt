<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class TourContent extends Model
{
    protected $table = 'en33_tours_contents';
    public $timestamps = false;

    protected $fillable = ['tour_id', 'lang', 'title', 'meta_desc', 'meta_key_words', 'desc', 'url'];

    /**
     * Decode HTML entities in title (e.g. &#039; -> ')
     */
    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? html_entity_decode($value, ENT_QUOTES, 'UTF-8') : $value,
        );
    }

    protected function desc(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? html_entity_decode($value, ENT_QUOTES, 'UTF-8') : $value,
        );
    }

    protected function metaDesc(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? html_entity_decode($value, ENT_QUOTES, 'UTF-8') : $value,
        );
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }
}
