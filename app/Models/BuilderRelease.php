<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuilderRelease extends Model
{
    use HasFactory;

    protected $fillable = [
        'server_id',
        'version',
        'status',
        'notes',
        'metadata',
        'deployed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'deployed_at' => 'datetime',
    ];

    /**
     * 릴리즈가 속한 서버
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * 성공한 릴리즈인지 확인
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    /**
     * 실패한 릴리즈인지 확인
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * 대기 중인 릴리즈인지 확인
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * 버전을 읽기 쉬운 형식으로 변환
     */
    public function getFormattedVersionAttribute(): string
    {
        $version = $this->version;
        if (strlen($version) === 14) {
            return substr($version, 0, 4) . '-' . 
                   substr($version, 4, 2) . '-' . 
                   substr($version, 6, 2) . ' ' . 
                   substr($version, 8, 2) . ':' . 
                   substr($version, 10, 2) . ':' . 
                   substr($version, 12, 2);
        }
        return $this->version;
    }

    /**
     * 상태별 배지 색상
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'success' => 'green',
            'failed' => 'red',
            'pending' => 'yellow',
            default => 'gray'
        };
    }
}

