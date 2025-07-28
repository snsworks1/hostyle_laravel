<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'price',
        'trial_days',
        'disk',
        'traffic',
        'domains',
        'subdomains',
        'databases',
        'emails'
    ];

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'feature_plan');
    }
}
