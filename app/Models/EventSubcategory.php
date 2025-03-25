<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSubcategory extends Model
{
    protected $table = 'events_subcategory';

    protected $fillable = [
        'name',
        'id_events_category', // Aici trebuie să fie id_events_category
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_subcategory', 'subcategory_id', 'event_id');
    }

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'id_events_category'); // Aici trebuie să fie id_events_category
    }
}