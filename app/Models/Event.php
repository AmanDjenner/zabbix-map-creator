<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'data',
        'id_institution',
        'id_events_category',
        'persons_involved',
        'events_text',
    ];

    protected $casts = [
        'data' => 'datetime',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'id_institution');
    }

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'id_events_category');
    }

    public function subcategories()
    {
        return $this->belongsToMany(EventSubcategory::class, 'event_subcategory', 'event_id', 'subcategory_id');
    }
}