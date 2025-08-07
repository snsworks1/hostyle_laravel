<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Server;

class CyberPanelService
{
    private $client;
    
    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            'verify' => false,
            'timeout' => 30
        ]);
    }
    
    /**
     * 보안 토큰 생성
     */
    private function generateSecurityToken($domain)
    {
        // 현재 시간을 5분 단위로 반올림 (토큰 유효시간 5분)
        $current_time = floor(time() / 300) * 300;
        $secret = 'CYBERPANEL_SECRET'; // CyberPanel과 동일한 시크릿
        
        return hash('sha256', $domain . ':' . $current_time . ':' . $secret);
    }
    
    /**
     * Laravel SSO 로그인 및 파일매니저 리디렉션 (보안 강화)
     */
    public function ssoLoginAndRedirect($server)
    {
        try {
            Log::info('Laravel SSO 로그인 시도', [
                'server_id' => $server->id,
                'domain' => $server->fqdn
            ]);
            
            // 1. CyberPanel 서버 정보 확인
            if (!$server->cyberpanelServer) {
                throw new \Exception('CyberPanel 서버 정보를 찾을 수 없습니다.');
            }
            
            // 2. CyberPanel URL 구성
            $protocol = $server->cyberpanelServer->ssl ? 'https' : 'http';
            $baseUrl = $protocol . '://' . $server->cyberpanelServer->host . ':' . $server->cyberpanelServer->api_port;
            
            // 3. 데이터베이스에서 자격 증명 가져오기
            $adminUser = $server->cyberpanelServer->api_user;
            $adminPass = $server->cyberpanelServer->api_password;
            $domain = $server->fqdn;
            
            Log::info('CyberPanel 자격 증명 정보', [
                'server_id' => $server->id,
                'admin_user' => $adminUser,
                'domain' => $domain,
                'base_url' => $baseUrl
            ]);
            
            // 4. 보안 토큰 생성
            $security_token = $this->generateSecurityToken($domain);
            
            // 5. 직접 파일매니저 접근 (보안 토큰 포함)
            $fileManagerUrl = $baseUrl . "/api/laravel/direct/{$domain}?token={$security_token}";
            
            Log::info('보안 토큰 생성', [
                'server_id' => $server->id,
                'domain' => $domain,
                'token' => $security_token,
                'file_manager_url' => $fileManagerUrl
            ]);
            
            // 6. 바로 파일매니저로 리디렉션
            Log::info('바로 파일매니저 리디렉션', [
                'server_id' => $server->id,
                'domain' => $domain,
                'file_manager_url' => $fileManagerUrl
            ]);
            
            return redirect()->away($fileManagerUrl);
            
        } catch (\Exception $e) {
            Log::error('Laravel SSO 로그인 실패', [
                'server_id' => $server->id,
                'error' => $e->getMessage()
            ]);
            
            throw new \Exception('SSO 로그인 실패: ' . $e->getMessage());
        }
    }
    

    
    /**
     * 세션 검증
     */
    public function verifySession($server)
    {
        try {
            $protocol = $server->cyberpanelServer->ssl ? 'https' : 'http';
            $baseUrl = $protocol . '://' . $server->cyberpanelServer->host . ':' . $server->cyberpanelServer->api_port;
            
            $response = Http::timeout(30)
                ->withoutVerifying()
                ->post($baseUrl . '/api/laravel/verify', [
                    'domain' => $server->fqdn
                ]);
            
            return $response->json();
            
        } catch (\Exception $e) {
            Log::error('세션 검증 실패', [
                'server_id' => $server->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'status' => 0,
                'error_message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * 연결 테스트
     */
    public function testConnection($server)
    {
        try {
            $protocol = $server->cyberpanelServer->ssl ? 'https' : 'http';
            $baseUrl = $protocol . '://' . $server->cyberpanelServer->host . ':' . $server->cyberpanelServer->api_port;
            
            $response = Http::timeout(30)
                ->withoutVerifying()
                ->post($baseUrl . '/api/verifyConn', [
                    'adminUser' => $server->cyberpanelServer->api_user,
                    'adminPass' => $server->cyberpanelServer->api_password
                ]);
            
            $result = $response->json();
            return isset($result['verifyConn']) && $result['verifyConn'] == 1;
            
        } catch (\Exception $e) {
            Log::error('연결 테스트 실패', [
                'server_id' => $server->id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
} 