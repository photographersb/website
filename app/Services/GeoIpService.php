<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeoIpService
{
    public function lookup(?string $ip): ?array
    {
        if (!$ip || !$this->isPublicIp($ip)) {
            return null;
        }

        return Cache::remember("geoip:{$ip}", 86400, function () use ($ip) {
            try {
                $response = Http::timeout(2)
                    ->acceptJson()
                    ->get("http://ip-api.com/json/{$ip}", [
                        'fields' => 'status,country,regionName,city,lat,lon,timezone,isp,query',
                    ]);

                if (!$response->ok()) {
                    return null;
                }

                $data = $response->json();
                if (($data['status'] ?? '') !== 'success') {
                    return null;
                }

                return [
                    'ip' => $data['query'] ?? $ip,
                    'country' => $data['country'] ?? null,
                    'region' => $data['regionName'] ?? null,
                    'city' => $data['city'] ?? null,
                    'lat' => $data['lat'] ?? null,
                    'lng' => $data['lon'] ?? null,
                    'timezone' => $data['timezone'] ?? null,
                    'isp' => $data['isp'] ?? null,
                ];
            } catch (\Throwable $e) {
                return null;
            }
        });
    }

    private function isPublicIp(string $ip): bool
    {
        return (bool) filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}
