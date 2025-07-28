<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Server;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TossPaymentService
{
    private $secretKey;
    private $baseUrl = 'https://api.tosspayments.com';

    public function __construct()
    {
        $this->secretKey = config('services.toss.secret_key');
    }

    /**
     * 토스페이먼츠 환불 처리
     */
    public function refundPayment($orderNumber, $amount, $reason = '사용자 요청')
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/v1/payments/' . $orderNumber . '/cancel', [
                'cancelReason' => $reason,
                'cancelAmount' => $amount,
                'refundReceiveAccount' => [
                    'bank' => 'KB',
                    'accountNumber' => '1234567890',
                    'holderName' => '홍길동'
                ]
            ]);

            if ($response->successful()) {
                Log::info('토스페이먼츠 환불 성공', [
                    'order_number' => $orderNumber,
                    'amount' => $amount,
                    'response' => $response->json()
                ]);

                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                Log::error('토스페이먼츠 환불 실패', [
                    'order_number' => $orderNumber,
                    'amount' => $amount,
                    'response' => $response->json()
                ]);

                return [
                    'success' => false,
                    'error' => $response->json()
                ];
            }
        } catch (\Exception $e) {
            Log::error('토스페이먼츠 환불 예외 발생', [
                'order_number' => $orderNumber,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 서버의 모든 주문에 대해 환불 처리
     */
    public function refundServer($serverId, $reason = '사용자 요청')
    {
        $server = Server::find($serverId);
        
        if (!$server) {
            return ['success' => false, 'error' => '서버를 찾을 수 없습니다.'];
        }

        $refundResults = [];
        $totalRefundAmount = 0;

        // 각 주문별로 환불 처리
        foreach ($server->activeOrders() as $order) {
            $refundAmount = $order->calculateRefundAmount();
            
            if ($refundAmount > 0) {
                $result = $this->refundPayment($order->order_number, $refundAmount, $reason);
                
                if ($result['success']) {
                    // 주문 상태를 환불됨으로 변경
                    $order->update(['status' => 'refunded']);
                    $totalRefundAmount += $refundAmount;
                }
                
                $refundResults[] = [
                    'order_number' => $order->order_number,
                    'order_type' => $order->order_type,
                    'refund_amount' => $refundAmount,
                    'result' => $result
                ];
            }
        }

        // 서버 상태 변경
        if ($totalRefundAmount > 0) {
            $server->update(['status' => 'cancelled']);
        }

        return [
            'success' => $totalRefundAmount > 0,
            'total_refund_amount' => $totalRefundAmount,
            'refund_results' => $refundResults
        ];
    }

    /**
     * 특정 주문만 환불 처리
     */
    public function refundOrder($orderId, $reason = '사용자 요청')
    {
        $order = Order::find($orderId);
        
        if (!$order) {
            return ['success' => false, 'error' => '주문을 찾을 수 없습니다.'];
        }

        $refundAmount = $order->calculateRefundAmount();
        
        if ($refundAmount <= 0) {
            return ['success' => false, 'error' => '환불 가능한 금액이 없습니다.'];
        }

        $result = $this->refundPayment($order->order_number, $refundAmount, $reason);
        
        if ($result['success']) {
            $order->update(['status' => 'refunded']);
            
            // 서버의 만료일 업데이트
            $order->server->updateExpiresAt();
        }

        return [
            'success' => $result['success'],
            'refund_amount' => $refundAmount,
            'result' => $result
        ];
    }
}


namespace App\Services;

use App\Models\Order;
use App\Models\Server;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TossPaymentService
{
    private $secretKey;
    private $baseUrl = 'https://api.tosspayments.com';

    public function __construct()
    {
        $this->secretKey = config('services.toss.secret_key');
    }

    /**
     * 토스페이먼츠 환불 처리
     */
    public function refundPayment($orderNumber, $amount, $reason = '사용자 요청')
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/v1/payments/' . $orderNumber . '/cancel', [
                'cancelReason' => $reason,
                'cancelAmount' => $amount,
                'refundReceiveAccount' => [
                    'bank' => 'KB',
                    'accountNumber' => '1234567890',
                    'holderName' => '홍길동'
                ]
            ]);

            if ($response->successful()) {
                Log::info('토스페이먼츠 환불 성공', [
                    'order_number' => $orderNumber,
                    'amount' => $amount,
                    'response' => $response->json()
                ]);

                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                Log::error('토스페이먼츠 환불 실패', [
                    'order_number' => $orderNumber,
                    'amount' => $amount,
                    'response' => $response->json()
                ]);

                return [
                    'success' => false,
                    'error' => $response->json()
                ];
            }
        } catch (\Exception $e) {
            Log::error('토스페이먼츠 환불 예외 발생', [
                'order_number' => $orderNumber,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 서버의 모든 주문에 대해 환불 처리
     */
    public function refundServer($serverId, $reason = '사용자 요청')
    {
        $server = Server::find($serverId);
        
        if (!$server) {
            return ['success' => false, 'error' => '서버를 찾을 수 없습니다.'];
        }

        $refundResults = [];
        $totalRefundAmount = 0;

        // 각 주문별로 환불 처리
        foreach ($server->activeOrders() as $order) {
            $refundAmount = $order->calculateRefundAmount();
            
            if ($refundAmount > 0) {
                $result = $this->refundPayment($order->order_number, $refundAmount, $reason);
                
                if ($result['success']) {
                    // 주문 상태를 환불됨으로 변경
                    $order->update(['status' => 'refunded']);
                    $totalRefundAmount += $refundAmount;
                }
                
                $refundResults[] = [
                    'order_number' => $order->order_number,
                    'order_type' => $order->order_type,
                    'refund_amount' => $refundAmount,
                    'result' => $result
                ];
            }
        }

        // 서버 상태 변경
        if ($totalRefundAmount > 0) {
            $server->update(['status' => 'cancelled']);
        }

        return [
            'success' => $totalRefundAmount > 0,
            'total_refund_amount' => $totalRefundAmount,
            'refund_results' => $refundResults
        ];
    }

    /**
     * 특정 주문만 환불 처리
     */
    public function refundOrder($orderId, $reason = '사용자 요청')
    {
        $order = Order::find($orderId);
        
        if (!$order) {
            return ['success' => false, 'error' => '주문을 찾을 수 없습니다.'];
        }

        $refundAmount = $order->calculateRefundAmount();
        
        if ($refundAmount <= 0) {
            return ['success' => false, 'error' => '환불 가능한 금액이 없습니다.'];
        }

        $result = $this->refundPayment($order->order_number, $refundAmount, $reason);
        
        if ($result['success']) {
            $order->update(['status' => 'refunded']);
            
            // 서버의 만료일 업데이트
            $order->server->updateExpiresAt();
        }

        return [
            'success' => $result['success'],
            'refund_amount' => $refundAmount,
            'result' => $result
        ];
    }
}
