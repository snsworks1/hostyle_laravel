<?php

namespace App\Http\Controllers;

use App\Models\SecurityEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SecurityEventController extends Controller
{
    /**
     * CyberPanel에서 보안 이벤트 수신
     */
    public function receiveEvent(Request $request)
    {
        try {
            // 요청 검증
            $validator = Validator::make($request->all(), [
                'event_type' => 'required|string',
                'domain' => 'required|string',
                'ip_address' => 'required|string',
                'user_agent' => 'nullable|string',
                'details' => 'nullable|array',
                'source' => 'required|string',
                'timestamp' => 'required|integer'
            ]);

            if ($validator->fails()) {
                Log::warning('Invalid security event data received', [
                    'errors' => $validator->errors(),
                    'data' => $request->all()
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid data format'
                ], 400);
            }

            // 심각도 결정
            $severity = $this->determineSeverity($request->event_type);

            // 보안 이벤트 저장
            $event = SecurityEvent::create([
                'event_type' => $request->event_type,
                'domain' => $request->domain,
                'ip_address' => $request->ip_address,
                'user_agent' => $request->user_agent,
                'details' => $request->details,
                'source' => $request->source,
                'severity' => $severity
            ]);

            // 로그 기록
            Log::info('Security event received from CyberPanel', [
                'event_id' => $event->id,
                'event_type' => $event->event_type,
                'domain' => $event->domain,
                'ip_address' => $event->ip_address,
                'severity' => $event->severity
            ]);

            // 심각한 이벤트인 경우 알림 처리
            if (in_array($severity, ['high', 'critical'])) {
                $this->handleCriticalEvent($event);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Security event recorded',
                'event_id' => $event->id
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error processing security event', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * 보안 이벤트 목록 조회
     */
    public function index(Request $request)
    {
        $query = SecurityEvent::query();

        // 필터링
        if ($request->has('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->has('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->has('domain')) {
            $query->where('domain', 'like', '%' . $request->domain . '%');
        }

        if ($request->has('ip_address')) {
            $query->where('ip_address', $request->ip_address);
        }

        if ($request->has('processed')) {
            $query->where('processed', $request->boolean('processed'));
        }

        // 정렬
        $query->orderBy('created_at', 'desc');

        // 페이지네이션
        $events = $query->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $events
        ]);
    }

    /**
     * 보안 이벤트 상세 조회
     */
    public function show(SecurityEvent $event)
    {
        return response()->json([
            'status' => 'success',
            'data' => $event
        ]);
    }

    /**
     * 보안 이벤트 처리 완료
     */
    public function markAsProcessed(SecurityEvent $event, Request $request)
    {
        $event->update([
            'processed' => true,
            'processed_at' => now(),
            'notes' => $request->notes
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Event marked as processed'
        ]);
    }

    /**
     * 보안 통계 조회
     */
    public function statistics(Request $request)
    {
        $days = $request->get('days', 7);
        
        $stats = [
            'total_events' => SecurityEvent::recent($days)->count(),
            'unprocessed_events' => SecurityEvent::recent($days)->unprocessed()->count(),
            'critical_events' => SecurityEvent::recent($days)->bySeverity('critical')->count(),
            'high_events' => SecurityEvent::recent($days)->bySeverity('high')->count(),
            'domain_tampering_events' => SecurityEvent::recent($days)->domainTampering()->count(),
            'token_events' => SecurityEvent::recent($days)->tokenEvents()->count(),
            'event_types' => SecurityEvent::recent($days)
                ->selectRaw('event_type, COUNT(*) as count')
                ->groupBy('event_type')
                ->get(),
            'top_ips' => SecurityEvent::recent($days)
                ->selectRaw('ip_address, COUNT(*) as count')
                ->groupBy('ip_address')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'top_domains' => SecurityEvent::recent($days)
                ->selectRaw('domain, COUNT(*) as count')
                ->groupBy('domain')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }

    /**
     * 심각도 결정
     */
    private function determineSeverity(string $eventType): string
    {
        return match($eventType) {
            'token_invalid' => 'high',
            'token_missing' => 'medium',
            'token_expired' => 'medium',
            'unauthorized_access' => 'critical',
            'suspicious_activity' => 'high',
            'domain_tampering' => 'critical',
            'invalid_domain' => 'high',
            default => 'medium'
        };
    }

    /**
     * 심각한 이벤트 처리
     */
    private function handleCriticalEvent(SecurityEvent $event)
    {
        // 여기에 알림 로직 추가 (이메일, 슬랙, 텔레그램 등)
        Log::warning('Critical security event detected', [
            'event_id' => $event->id,
            'event_type' => $event->event_type,
            'domain' => $event->domain,
            'ip_address' => $event->ip_address,
            'details' => $event->details
        ]);

        // 도메인 변조 이벤트인 경우 특별 처리
        if ($event->event_type === 'domain_tampering') {
            $this->handleDomainTamperingEvent($event);
        }
    }

    /**
     * 도메인 변조 이벤트 특별 처리
     */
    private function handleDomainTamperingEvent(SecurityEvent $event)
    {
        Log::critical('DOMAIN TAMPERING DETECTED!', [
            'event_id' => $event->id,
            'domain' => $event->domain,
            'ip_address' => $event->ip_address,
            'user_agent' => $event->user_agent,
            'details' => $event->details
        ]);

        // 여기에 추가 보안 조치를 구현할 수 있습니다:
        // 1. 해당 IP 차단
        // 2. 관리자에게 즉시 알림
        // 3. 관련 도메인 접근 제한
        // 4. 보안 로그 강화
    }
}
