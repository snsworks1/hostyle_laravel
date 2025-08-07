<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'domain',
        'ip_address',
        'user_agent',
        'details',
        'source',
        'severity',
        'processed',
        'processed_at',
        'notes'
    ];

    protected $casts = [
        'details' => 'array',
        'processed' => 'boolean',
        'processed_at' => 'datetime'
    ];

    /**
     * 이벤트 타입별 아이콘
     */
    public function getEventIconAttribute(): string
    {
        return match($this->event_type) {
            'token_invalid' => '🔐',
            'token_missing' => '🔑',
            'unauthorized_access' => '🚨',
            'suspicious_activity' => '⚠️',
            'domain_tampering' => '🎭',
            'invalid_domain' => '❌',
            'token_expired' => '⏰',
            default => '📝'
        };
    }

    /**
     * 심각도별 색상
     */
    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'low' => 'text-green-600',
            'medium' => 'text-yellow-600',
            'high' => 'text-orange-600',
            'critical' => 'text-red-600',
            default => 'text-gray-600'
        };
    }

    /**
     * 미처리 이벤트 필터링
     */
    public function scopeUnprocessed($query)
    {
        return $query->where('processed', false);
    }

    /**
     * 최근 이벤트 필터링
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * 심각도별 필터링
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * 도메인 변조 이벤트 필터링
     */
    public function scopeDomainTampering($query)
    {
        return $query->where('event_type', 'domain_tampering');
    }

    /**
     * 토큰 관련 이벤트 필터링
     */
    public function scopeTokenEvents($query)
    {
        return $query->whereIn('event_type', ['token_invalid', 'token_missing', 'token_expired']);
    }
}
