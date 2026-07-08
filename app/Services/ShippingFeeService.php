<?php

namespace App\Services;

use App\Models\ShippingAddress;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ShippingFeeService
{
    public function quoteForAddress(ShippingAddress $address): array
    {
        if (!$address->latitude || !$address->longitude) {
            throw new RuntimeException('Vui lòng chọn vị trí giao hàng trên Google Maps.');
        }

        $route = $this->resolveDrivingRoute((float) $address->latitude, (float) $address->longitude);
        $distanceKm = round($route['distance_meters'] / 1000, 2);
        $maxDistanceKm = (float) config('services.google_maps.max_delivery_distance_km', 40);

        if ($distanceKm > $maxDistanceKm) {
            throw new RuntimeException("Cửa hàng chỉ giao trong phạm vi {$maxDistanceKm}km.");
        }

        return [
            'available' => true,
            'distance_km' => $distanceKm,
            'duration_seconds' => $route['duration_seconds'],
            'shipping_fee' => $this->calculateFee($distanceKm),
            'message' => null,
        ];
    }

    private function fetchDrivingRoute(float $destinationLatitude, float $destinationLongitude): array
    {
        $apiKey = config('services.google_maps.server_key');

        if (!$apiKey) {
            throw new RuntimeException('Chưa cấu hình Google Maps server API key.');
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Goog-Api-Key' => $apiKey,
                    'X-Goog-FieldMask' => 'routes.distanceMeters,routes.duration',
                ])
                ->post('https://routes.googleapis.com/directions/v2:computeRoutes', [
                    'origin' => [
                        'location' => [
                            'latLng' => [
                                'latitude' => (float) config('services.google_maps.store_latitude'),
                                'longitude' => (float) config('services.google_maps.store_longitude'),
                            ],
                        ],
                    ],
                    'destination' => [
                        'location' => [
                            'latLng' => [
                                'latitude' => $destinationLatitude,
                                'longitude' => $destinationLongitude,
                            ],
                        ],
                    ],
                    'travelMode' => 'DRIVE',
                    'routingPreference' => 'TRAFFIC_UNAWARE',
                    'computeAlternativeRoutes' => false,
                    'languageCode' => 'vi-VN',
                    'units' => 'METRIC',
                ]);
        } catch (\Throwable $exception) {
            Log::warning('Google Routes API connection failed.', [
                'message' => $exception->getMessage(),
            ]);

            throw new RuntimeException('Không thể kết nối Google Maps để tính phí giao hàng.');
        }

        if (!$response->successful()) {
            Log::warning('Google Routes API returned an error.', [
                'status' => $response->status(),
                'body' => $response->json() ?: $response->body(),
            ]);

            throw new RuntimeException('Không thể tính khoảng cách giao hàng từ Google Maps.');
        }

        $route = $response->json('routes.0');

        if (!$route || !array_key_exists('distanceMeters', $route)) {
            throw new RuntimeException('Không tìm thấy tuyến đường giao hàng phù hợp.');
        }

        return [
            'distance_meters' => (int) $route['distanceMeters'],
            'duration_seconds' => $this->parseGoogleDuration($route['duration'] ?? null),
        ];
    }

    private function resolveDrivingRoute(float $destinationLatitude, float $destinationLongitude): array
    {
        try {
            return $this->fetchDrivingRoute($destinationLatitude, $destinationLongitude);
        } catch (RuntimeException $exception) {
            Log::warning('Google Routes API failed. Falling back to estimated delivery distance.', [
                'message' => $exception->getMessage(),
            ]);

            return $this->fetchEstimatedRoute($destinationLatitude, $destinationLongitude);
        }
    }

    private function fetchEstimatedRoute(float $destinationLatitude, float $destinationLongitude): array
    {
        $originLatitude = (float) config('services.google_maps.store_latitude');
        $originLongitude = (float) config('services.google_maps.store_longitude');

        try {
            $response = Http::timeout(5)->get(
                "https://router.project-osrm.org/route/v1/driving/{$originLongitude},{$originLatitude};{$destinationLongitude},{$destinationLatitude}",
                ['overview' => 'false']
            );

            $distance = $response->json('routes.0.distance');

            if ($response->successful() && is_numeric($distance)) {
                return [
                    'distance_meters' => (int) round((float) $distance),
                    'duration_seconds' => is_numeric($response->json('routes.0.duration'))
                        ? (int) round((float) $response->json('routes.0.duration'))
                        : null,
                ];
            }

            Log::warning('OSRM route fallback returned an invalid response.', [
                'status' => $response->status(),
                'body' => $response->json() ?: $response->body(),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('OSRM route fallback failed. Using straight-line delivery distance estimate.', [
                'message' => $exception->getMessage(),
            ]);
        }

        $multiplier = (float) config('services.google_maps.fallback_road_multiplier', 1.3);
        $distanceKm = $this->calculateStraightLineDistanceKm(
            $originLatitude,
            $originLongitude,
            $destinationLatitude,
            $destinationLongitude
        ) * $multiplier;

        return [
            'distance_meters' => (int) round($distanceKm * 1000),
            'duration_seconds' => null,
        ];
    }

    private function calculateFee(float $distanceKm): float
    {
        $baseFee = (float) config('services.google_maps.base_fee', 10000);
        $baseDistanceKm = (float) config('services.google_maps.base_distance_km', 3);
        $perKmFee = (float) config('services.google_maps.per_km_fee', 5000);

        if ($distanceKm <= $baseDistanceKm) {
            return $baseFee;
        }

        return $baseFee + (ceil($distanceKm - $baseDistanceKm) * $perKmFee);
    }

    private function parseGoogleDuration(?string $duration): ?int
    {
        if (!$duration || !str_ends_with($duration, 's')) {
            return null;
        }

        return (int) rtrim($duration, 's');
    }

    private function calculateStraightLineDistanceKm(
        float $originLatitude,
        float $originLongitude,
        float $destinationLatitude,
        float $destinationLongitude
    ): float {
        $earthRadiusKm = 6371;
        $latitudeDelta = deg2rad($destinationLatitude - $originLatitude);
        $longitudeDelta = deg2rad($destinationLongitude - $originLongitude);

        $a = (sin($latitudeDelta / 2) ** 2)
            + cos(deg2rad($originLatitude))
            * cos(deg2rad($destinationLatitude))
            * (sin($longitudeDelta / 2) ** 2);

        $centralAngle = 2 * atan2(sqrt(min(1, $a)), sqrt(max(0, 1 - $a)));

        return $earthRadiusKm * $centralAngle;
    }
}
