<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InjuryCategory extends Model
{
    protected $table = 'injuries_category'; // Specificăm tabela

    protected $fillable = [
        'name', // Câmpurile care pot fi completate
    ];

    // Relație cu tabela injuries
    public function injuries()
    {
        return $this->hasMany(Injury::class, 'id_injuries_category');
    }
}