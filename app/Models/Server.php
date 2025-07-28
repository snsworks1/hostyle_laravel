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
        'status'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'total_paid_amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
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
}
