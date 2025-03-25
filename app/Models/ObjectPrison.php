<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjectPrison extends Model
{
    protected $table = 'object_prisons'; // Explicit pentru siguranță

    protected $fillable = [
        'data',
        'id_institution',
        'eveniment',
        'obj_text',
        'created_by',
        'updated_by',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'id_institution');
    }

    public function objectListItems()
    {
        return $this->belongsToMany(ObjectList::class, 'object_prison_objects', 'object_prison_id', 'object_list_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}