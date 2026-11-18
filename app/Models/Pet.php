<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'species',
        'breed',
        'gender',
        'birth_date',
        'weight',
        'photo',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(CareSchedule::class);
    }

    public function healthRecords()
    {
        return $this->hasMany(HealthRecord::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function weightLogs()
    {
        return $this->hasMany(WeightLog::class);
    }

    public function getWeightAttribute()
    {
        return $this->weightLogs()->latest()->value('weight');
    }
}