<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'server_id',
        'order_number',
        'order_type',
        'amount',
        'months',
        'plan',
        'status',
        'expires_at',
        'payment_data'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'payment_data' => 'array',
        'amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * 주문번호 생성
     */
    public static function generateOrderNumber($type)
    {
        $prefix = $type === 'purchase' ? 'PUR' : 'EXT';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        
        return "{$prefix}_{$date}_{$random}";
    }

    /**
     * 만료일 계산
     */
    public function calculateExpiresAt($startDate = null)
    {
        $start = $startDate ? Carbon::parse($startDate) : now();
        return $start->addMonths($this->months);
    }

    /**
     * 환불 가능 여부 확인
     */
    public function isRefundable()
    {
        if ($this->status !== 'paid') {
            return false;
        }

        $usedDays = now()->diffInDays($this->created_at);
        
        // 연장 주문은 아직 사용하지 않았으면 100% 환불 가능
        if ($this->order_type === 'extension' && $this->expires_at > now()) {
            return true;
        }

        // 구매 주문은 14일 이내만 환불 가능
        if ($this->order_type === 'purchase' && $usedDays <= 14) {
            return true;
        }

        return false;
    }

    /**
     * 환불 금액 계산
     */
    public function calculateRefundAmount()
    {
        if (!$this->isRefundable()) {
            return 0;
        }

        // 연장 주문은 100% 환불
        if ($this->order_type === 'extension' && $this->expires_at > now()) {
            return $this->amount;
        }

        // 구매 주문은 일할 계산
        $usedDays = now()->diffInDays($this->created_at);
        $monthlyPrice = $this->amount / $this->months;
        $usedAmount = ($monthlyPrice / 30) * $usedDays;
        
        return max(0, $this->amount - $usedAmount);
    }
}
