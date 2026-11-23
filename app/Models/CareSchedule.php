<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'title',
        'category',
        'scheduled_date',
        'scheduled_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}