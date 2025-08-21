<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuilderPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'server_id',
        'parent_id',
        'type',
        'name',
        'slug',
        'seq',
        'read_level',
        'menu_hide',
        'hide_header',
        'hide_footer',
        'is_main',
        'page_schema',
        'site_tokens',
        'preview_html',
        'preview_css',
        'published_html',
        'published_css',
        'is_published',
        'published_at',
        'seo_title',
        'seo_keywords',
        'seo_description',
        'seo_search_ban'
    ];

    protected $casts = [
        'page_schema' => 'array',
        'site_tokens' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'menu_hide' => 'boolean',
        'hide_header' => 'boolean',
        'hide_footer' => 'boolean',
        'is_main' => 'boolean',
        'seo_search_ban' => 'boolean',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function parent()
    {
        return $this->belongsTo(BuilderPage::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(BuilderPage::class, 'parent_id')->orderBy('seq');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function scopeRoot($query)
    {
        return $query->where('parent_id', 0)->orWhereNull('parent_id');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }
}
