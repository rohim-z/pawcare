<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeightLog extends Model
{
    protected $fillable = ['pet_id', 'weight', 'recorded_date'];
}
