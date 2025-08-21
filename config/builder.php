<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Builder Upload Configuration
    |--------------------------------------------------------------------------
    |
    | JWT 토큰 설정 및 업로드 관련 설정
    |
    */
    'upload_jwt_secret' => env('BUILDER_UPLOAD_JWT_SECRET', 'change_me'),
    'upload_jwt_ttl_minutes' => env('BUILDER_UPLOAD_JWT_TTL_MINUTES', 5),
    
    /*
    |--------------------------------------------------------------------------
    | Release Management
    |--------------------------------------------------------------------------
    |
    | 릴리즈 관리 설정
    |
    */
    'default_release_keep' => env('BUILDER_DEFAULT_RELEASE_KEEP', 10),
    
    /*
    |--------------------------------------------------------------------------
    | Path Conventions
    |--------------------------------------------------------------------------
    |
    | 경로 규약 설정
    |
    */
    'assets_base' => '/assets',
    'uploads_base' => '/assets/uploads',
    
    /*
    |--------------------------------------------------------------------------
    | Default Site Tokens
    |--------------------------------------------------------------------------
    |
    | 기본 사이트 토큰 설정
    |
    */
    'default_tokens' => [
        'brandColor' => '#111111',
        'contentWidth' => '1080px',
        'fontBase' => 'Noto Sans KR, sans-serif',
        'radius' => '12px'
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Available Sections
    |--------------------------------------------------------------------------
    |
    | 사용 가능한 섹션 설정
    |
    */
    'available_sections' => [
        'hero' => [
            'name' => '히어로 섹션',
            'icon' => 'fas fa-star',
            'component' => 'Hero'
        ],
        'features' => [
            'name' => '특징 섹션',
            'icon' => 'fas fa-th-large',
            'component' => 'Features'
        ],
        'cta' => [
            'name' => 'CTA 섹션',
            'icon' => 'fas fa-bullhorn',
            'component' => 'Cta'
        ]
    ],
    
    /*
    |--------------------------------------------------------------------------
    | SFTP Configuration
    |--------------------------------------------------------------------------
    |
    | SFTP 배포 설정
    |
    */
    'sftp' => [
        'timeout' => 30,
        'retry_attempts' => 3,
        'retry_delay' => 5,
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Build Configuration
    |--------------------------------------------------------------------------
    |
    | 빌드 설정
    |
    */
    'build' => [
        'temp_dir' => 'storage/app/builder/tmp',
        'max_file_size' => 10 * 1024 * 1024, // 10MB
        'allowed_mime_types' => [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf', 'text/plain', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ],
    ],
];

