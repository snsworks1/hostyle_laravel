<?php

return [
    'api_url' => env('CYBERPANEL_API_URL', 'https://cyberpanel.example.com'),
    'panel_url' => env('CYBERPANEL_PANEL_URL', 'https://cyberpanel.example.com'),
    'api_token' => env('CYBERPANEL_API_TOKEN'),
    'timeout' => env('CYBERPANEL_TIMEOUT', 30),
    'cache_ttl' => env('CYBERPANEL_CACHE_TTL', 1800), // 30분
]; 