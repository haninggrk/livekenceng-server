<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShopeeService
{
    /**
     * Get live stream session IDs from Shopee Creator API
     *
     * @param  string  $cookie  Shopee account cookie
     * @return array Array of session IDs
     */
    public function randomClientInfo(): string
    {
        $devicesModel = ['iPhone13,1', 'iPhone13,2', 'iPhone13,3', 'iPhone13,4', 'iPhone13,5', 'iPhone13,6', 'iPhone13,7', 'iPhone13,8', 'iPhone13,9', 'iPhone13,10'];
        // 7A7903CD8CE8463FBBA8753FB76578B6 MUST LIKE THIS
        $deviceId = '7A7903CD8CE8463FBBA8753FB76578B6';
        // randomize the order of the deviceId
        $deviceId = str_shuffle($deviceId);
        $randomDeviceModel = $devicesModel[array_rand($devicesModel)];

        return "device_id={$deviceId};device_model={$randomDeviceModel};os=1;os_version=18.5;client_version=35626;platform=4;app_type=1;language=id;";
    }

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
                } elseif (isset($data['code']) && $data['code'] === 100003) {
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
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get active live stream session data (status = 1) including GMV
     *
     * @param  string  $cookie  Shopee account cookie
     * @return array|null Active session data (session_id, gmv) or null if no active session
     */
    public function getActiveSessionData(string $cookie): ?array
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
                            return [
                                'session_id' => (string) $record['sessionId'],
                                'gmv' => $record['confirmedSales'] ?? $record['placedSales'] ?? 0,
                                'views' => $record['views'] ?? 0,
                                'likes' => $record['likes'] ?? 0,
                                'comments' => $record['comments'] ?? 0,
                                'atc' => $record['atc'] ?? 0,
                                'placed_orders' => $record['placedOrders'] ?? 0,
                                'confirmed_orders' => $record['confirmedOrders'] ?? 0,
                            ];
                        }
                    }

                    // No active session found
                    return null;
                } elseif (isset($data['code']) && $data['code'] === 100003) {
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
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get active live stream session ID (status = 1)
     *
     * @param  string  $cookie  Shopee account cookie
     * @return string|null Active session ID or null if no active session
     */
    public function getActiveSessionId(string $cookie): ?string
    {
        $data = $this->getActiveSessionData($cookie);

        return $data['session_id'] ?? null;
    }

    /**
     * Replace products in live stream
     *
     * @param  string  $cookie  Shopee account cookie
     * @param  string  $sessionId  Live stream session ID
     * @param  array  $items  Array of items with shop_id and item_id
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
                'client-info' => $this->randomClientInfo(),
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
                'message' => 'Error replacing products: '.$e->getMessage(),
            ];
        }
    }
}
