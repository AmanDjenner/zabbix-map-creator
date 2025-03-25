<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detinuti extends Model
{
    protected $table = 'detinuti';
    protected $fillable = [
        'data',
        'id_institution',
        'total',
        'real_inmates',
        'in_search',
        'pretrial_detention',
        'initial_conditions',
        'life',
        'female',
        'minors',
        'open_sector',
        'no_escort',
        'monitoring_bracelets',
        'hunger_strike',
        'disciplinary_insulator',
        'admitted_to_hospitals',
        'employed_ip_in_hospitals',
        'employed_dds_in_hospitals',
        'work_outside',
        'employed_ip_work_outside',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'id_institution');
    }
}