<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuilderTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'thumbnail',
        'description',
        'page_schema',
        'site_tokens',
        'is_active'
    ];

    protected $casts = [
        'page_schema' => 'array',
        'site_tokens' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

