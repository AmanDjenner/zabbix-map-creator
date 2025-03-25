<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjectList extends Model
{
    protected $table = 'object_list';

    protected $fillable = [
        'name',
        
    ];

    public function objectPrisons()
    {
        return $this->belongsToMany(ObjectPrison::class, 'object_prison_objects', 'object_list_id', 'object_prison_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}