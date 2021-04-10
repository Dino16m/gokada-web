<?php

namespace App\Services;

use App\Models\Address;
use Illuminate\Support\Facades\Http;

class GeocodingService
{
    private string $apiKey;
    private string $url;

    public function __construct(string $apiKey, string $url)
    {
        $this->apiKey = $apiKey;
        $this->url = $url;
    }

    public function geocode(string $placeId)
    {
        $data = $this->getFromCache($placeId);
        if (!$data['lat'] || !$data['lng']) {
            $data = $this->fetchLocation($placeId);
            $this->putInCache($placeId, $data['lat'], $data['lng']);
        }
        return $data;
    }

    private function getFromCache(string $placeId)
    {
        $address = Address::where('placeId', $placeId)->first();
        if ($address) {
            return ['lat' => $address->lat, 'lng' => $address->lng];
        }

        return ['lat' => null, 'lng' => null];
    }

    private function putInCache(string $placeId, float $lat, float $lng)
    {
        $address = Address::where('placeId', $placeId)->first();
        if ($address) {
            $address->lat = $lat;
            $address->lng = $lng;
            $address->save();
        }
    }

    private function fetchLocation(string $placeId)
    {
        $response = Http::get($this->url, [
            'key' => $this->apiKey,
            'place_id' => $placeId,
        ]);
        $data = $response->json();
        if (strtolower($data['status']) != 'ok') {
            throw new \Exception("Location not found for $placeId");
        }
        $result = $data['results'][0];

        return $result['geometry']['location'];
    }
}
