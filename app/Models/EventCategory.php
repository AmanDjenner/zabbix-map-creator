<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    protected $table = 'events_category';

    protected $fillable = [
        'name',
    ];

    public function subcategories()
    {
        return $this->hasMany(EventSubcategory::class, 'id_events_category');
    }
}