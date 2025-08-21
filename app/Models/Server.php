<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'site_name',
        'domain',
        'fqdn',
        'region',
        'platform',
        'php_version',
        'plan',
        'months',
        'expires_at',
        'cyberpanel_server_id',
        'total_months',
        'total_paid_amount',
        'status',
        'builder_enabled',
        'builder_settings'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'total_paid_amount' => 'decimal:2',
        'builder_settings' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cyberpanelServer()
    {
        return $this->belongsTo(CyberpanelServer::class, 'cyberpanel_server_id');
    }

    public function builderPages()
    {
        return $this->hasMany(BuilderPage::class);
    }

    public function builderReleases()
    {
        return $this->hasMany(BuilderRelease::class);
    }

    /**
     * 기본 메인 페이지 생성
     */
    public function createDefaultMainPage()
    {
        return $this->builderPages()->firstOrCreate(
            ['slug' => 'main'],
            [
                'name' => '메인 페이지',
                'type' => 'page',
                'slug' => 'main',
                'is_main' => true,
                'page_schema' => [
                    'sections' => [
                        [
                            'type' => 'hero',
                            'props' => [
                                'title' => '환영합니다',
                                'subtitle' => '당신의 웹사이트에 오신 것을 환영합니다',
                                'align' => 'center',
                                'py' => 80
                            ]
                        ]
                    ]
                ],
                'site_tokens' => [
                    'brandColor' => '#111111',
                    'contentWidth' => '1080px',
                    'fontBase' => 'Noto Sans KR, sans-serif',
                    'radius' => '12px'
                ]
            ]
        );
    }

    /**
     * 활성 주문들 (환불되지 않은 주문들)
     */
    public function activeOrders()
    {
        return $this->orders()->where('status', 'paid');
    }

    /**
     * 구매 주문
     */
    public function purchaseOrder()
    {
        return $this->orders()->where('order_type', 'purchase')->where('status', 'paid')->first();
    }

    /**
     * 연장 주문들
     */
    public function extensionOrders()
    {
        return $this->orders()->where('order_type', 'extension')->where('status', 'paid')->get();
    }

    /**
     * 만료일 업데이트
     */
    public function updateExpiresAt()
    {
        $purchaseOrder = $this->purchaseOrder();
        if (!$purchaseOrder) {
            return;
        }

        $expiresAt = $purchaseOrder->expires_at;
        
        // 연장 주문들의 만료일을 추가
        foreach ($this->extensionOrders() as $extensionOrder) {
            $expiresAt = $extensionOrder->calculateExpiresAt($expiresAt);
        }

        $this->update(['expires_at' => $expiresAt]);
    }

    /**
     * 총 환불 금액 계산
     */
    public function calculateTotalRefundAmount()
    {
        $totalRefund = 0;
        
        foreach ($this->activeOrders() as $order) {
            $totalRefund += $order->calculateRefundAmount();
        }
        
        return $totalRefund;
    }

    /**
     * 환불 가능 여부 확인
     */
    public function isRefundable()
    {
        return $this->calculateTotalRefundAmount() > 0;
    }

    /**
     * SFTP 호스트 정보 가져오기 (cyberpanel_servers 테이블에서)
     */
    public function getSftpHost()
    {
        return $this->cyberpanelServer?->host;
    }

    /**
     * SFTP 포트 정보 가져오기 (cyberpanel_servers 테이블에서)
     */
    public function getSftpPort()
    {
        return $this->cyberpanelServer?->api_port ?? 22;
    }

    /**
     * SFTP 사용자명 가져오기 (cyberpanel_servers 테이블에서)
     */
    public function getSftpUsername()
    {
        return $this->cyberpanelServer?->ssh_user;
    }

    /**
     * SFTP 비밀번호 가져오기 (cyberpanel_servers 테이블에서)
     */
    public function getSftpPassword()
    {
        return $this->cyberpanelServer?->mysql_password;
    }

    /**
     * SFTP 키 파일 경로 가져오기 (cyberpanel_servers 테이블에서)
     */
    public function getSftpKeyPath()
    {
        return $this->cyberpanelServer?->ssh_key_path;
    }

    /**
     * 문서 루트 경로 생성 (cyberpanel 계정 정보 기반)
     */
    public function getDocroot()
    {
        // cyberpanel에서 계정명을 가져와서 경로 생성
        $accountName = $this->domain; // 서브도메인을 계정명으로 사용
        return "/home/{$accountName}/public_html";
    }

    /**
     * 홈 경로 생성 (cyberpanel 계정 정보 기반)
     */
    public function getHomePath()
    {
        $accountName = $this->domain;
        return "/home/{$accountName}";
    }
}
