<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Injury extends Model
{
    protected $table = 'injuries';

    protected $fillable = [
        'data',
        'id_institution',
        'id_injuries_category',
        'persons_involved',
        'injuries_text',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'id_institution');
    }

    public function injuryCategory()
    {
        return $this->belongsTo(InjuryCategory::class, 'id_injuries_category');
    }
}