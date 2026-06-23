<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'en33_pages';
    public $timestamps = false;

    protected $fillable = [
        'name', 'title', 'meta_desc', 'meta_key', 'contents',
        'published', 'lang_id', 'lang', 'icon', 'layout', 'url',
    ];
}
