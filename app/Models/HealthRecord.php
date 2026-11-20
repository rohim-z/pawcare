<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthRecord extends Model
{
    protected $fillable = [
        'pet_id',
        'record_type',
        'title',
        'record_date',
        'vet_name',
        'diagnosis_or_notes',
        'next_visit_date',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}