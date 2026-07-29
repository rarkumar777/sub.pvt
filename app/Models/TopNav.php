<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopNav extends Model
{
    protected $table = 'en33_topnav';
    public $timestamps = false;

    protected $fillable = [
        'lang_id', 'lang', 'label', 'link', 'parent_id', 'target', 'icon', 'link_order',
    ];

    public function parent()
    {
        return $this->belongsTo(TopNav::class, 'parent_id', 'lang_id');
    }

    public function children()
    {
        return $this->hasMany(TopNav::class, 'parent_id', 'lang_id');
    }
}
