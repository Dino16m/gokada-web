<?php
namespace App\Services;

use App\Models\Address;
use Illuminate\Support\Facades\Http;

class ReverseGeocodeService{
    private string $apiKey;
    private string $url;

    public function __construct(string $apiKey, string $url)
    {
        $this->apiKey = $apiKey;
        $this->url = $url;
    }

    function reverse(float $lat, float $lng){
        $address = $this->getFromCache($lat, $lng);
        if (!$address) {
            $address = $this->fetchAddress($lat, $lng);
            $this->putInCache($address);
        }
        return $address;
    }
    private function getFromCache(float $lat, float $lng){
        $address = Address::where("lat", $lat)->where("lng", $lng)->first();
        if ($address) {
            return $address->toArray();
        }
        return;
    }

    private function putInCache(array $address){
        Address::create($address);
    }

    private function fetchAddress(float $lat, float $lng){
        $response = Http::get($this->url, [
            "key" => $this->apiKey,
            "latlng"=> "$lat,$lng"
        ]);
        $data = $response->json();

        if($data["status"] != "OK"){
            throw new \Exception($data["status"]);
        }

        $result = $data["results"][0];
        $addressTexts = \explode(",", $result["formatted_address"], 2);
        return [
            "placeId" => $result["place_id"],
            "lat" => $result["geometry"]["location"]["lat"],
            "lng" => $result["geometry"]["location"]["lng"],
            "primaryText" => $addressTexts[0],
            "secondaryText" => $addressTexts[1],
        ];
    }
}