<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShopeeService
{
    /**
     * Get live stream session IDs from Shopee Creator API
     * 
     * @param string $cookie Shopee account cookie
     * @return array Array of session IDs
     */
    public function getSessionIds(string $cookie): array
    {
        try {
            $response = Http::withHeaders([
                'Cookie' => $cookie,
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept' => 'application/json, text/plain, */*',
                'Accept-Language' => 'id-ID,id;q=0.9,en;q=0.8',
                'Referer' => 'https://creator.shopee.co.id/',
                'Origin' => 'https://creator.shopee.co.id',
            ])->get('https://creator.shopee.co.id/supply/api/lm/sellercenter/realtime/sessionList', [
                'page' => 1,
                'pageSize' => 10,
                'name' => '',
                'orderBy' => '',
                'sort' => '',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['code']) && $data['code'] === 0 && isset($data['data']['list'])) {
                    $sessionIds = array_filter(
                        array_map(function ($record) {
                            return $record['sessionId'] ?? null;
                        }, $data['data']['list'])
                    );
                    
                    return array_values($sessionIds);
                } else if (isset($data['code']) && $data['code'] === 100003) {
                    Log::warning('Shopee API: Authentication failed', ['cookie_length' => strlen($cookie)]);
                    return [];
                } else {
                    Log::warning('Shopee API: Error code returned', ['code' => $data['code'] ?? 'unknown']);
                    return [];
                }
            }

            Log::error('Shopee API: Request failed', ['status' => $response->status()]);
            return [];
        } catch (\Exception $e) {
            Log::error('Shopee API: Exception while fetching session IDs', [
                'message' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get active live stream session ID (status = 1)
     * 
     * @param string $cookie Shopee account cookie
     * @return string|null Active session ID or null if no active session
     */
    public function getActiveSessionId(string $cookie): ?string
    {
        try {
            $response = Http::withHeaders([
                'Cookie' => $cookie,
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept' => 'application/json, text/plain, */*',
                'Accept-Language' => 'id-ID,id;q=0.9,en;q=0.8',
                'Referer' => 'https://creator.shopee.co.id/',
                'Origin' => 'https://creator.shopee.co.id',
            ])->get('https://creator.shopee.co.id/supply/api/lm/sellercenter/realtime/sessionList', [
                'page' => 1,
                'pageSize' => 10,
                'name' => '',
                'orderBy' => '',
                'sort' => '',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['code']) && $data['code'] === 0 && isset($data['data']['list'])) {
                    // Find first session with status = 1 (active/live)
                    foreach ($data['data']['list'] as $record) {
                        if (isset($record['status']) && $record['status'] === 1 && isset($record['sessionId'])) {
                            return (string)$record['sessionId'];
                        }
                    }
                    
                    // No active session found
                    return null;
                } else if (isset($data['code']) && $data['code'] === 100003) {
                    Log::warning('Shopee API: Authentication failed', ['cookie_length' => strlen($cookie)]);
                    return null;
                } else {
                    Log::warning('Shopee API: Error code returned', ['code' => $data['code'] ?? 'unknown']);
                    return null;
                }
            }

            Log::error('Shopee API: Request failed', ['status' => $response->status()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Shopee API: Exception while fetching active session', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Replace products in live stream
     * 
     * @param string $cookie Shopee account cookie
     * @param string $sessionId Live stream session ID
     * @param array $items Array of items with shop_id and item_id
     * @return array Response with success status and message
     */
    public function replaceProducts(string $cookie, string $sessionId, array $items): array
    {
        try {
            $response = Http::withHeaders([
                'accept' => '*/*',
                'accept-encoding' => 'gzip, deflate, br',
                'accept-language' => 'id-ID,id,en-US,en',
                'content-type' => 'application/json',
                'Cookie' => $cookie,
                'user-agent' => 'language=id app_type=1 platform=native_ios appver=35945 os_ver=18.6.2 Cronet/102.0.5005.61',
                'x-livestreaming-source' => 'shopee',
                'x-shopee-client-timezone' => 'Asia/Jakarta',
            ])->timeout(15)->put("https://live.shopee.co.id/api/v1/session/{$sessionId}/items", [
                'items' => array_map(function ($item) {
                    return [
                        'shop_id' => $item['shop_id'],
                        'item_id' => $item['item_id'],
                    ];
                }, $items),
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Products replaced successfully',
                ];
            } else {
                Log::error('Shopee API: Failed to replace products', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Failed to replace products',
                    'error' => $response->body(),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Shopee API: Exception while replacing products', [
                'message' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'message' => 'Error replacing products: ' . $e->getMessage(),
            ];
        }
    }
}

