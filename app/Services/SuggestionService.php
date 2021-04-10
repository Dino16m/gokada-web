<?php
namespace App\Services;

use App\Models\Query;
use App\Models\Address;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class SuggestionService{
    private string $apiKey;
    private string $url;

    function __construct(string $apiKey, string $url){
        $this->apiKey = $apiKey;
        $this->url = $url;
    }

    public function getSuggestions(
        string $query, float $lat, float $lng, float $radius
    ){
        $data = $this->getFromCache($query, $lat, $lng, $radius);
        if($data->isEmpty()){
            $data = $this->fetchSuggestions($query, $lat, $lng, $radius);
            $this->putInCache($data, $query, $lat, $lng,);
        }
        return $data;
    }

    private function fetchSuggestions(
        string $query, float $lat, float $lng, float $radius
    ){
        $response = Http::get($this->url, [
            "input" => $query,
            "key" => $this->apiKey,
            "location" => "$lat,$lng",
            "radius" => $radius,
            "strictbounds" => true,
        ]);
        $data = $response->json();
        if (strtolower($data["status"]) != "ok") return collect([]);
        return \collect($data["predictions"])->map(function($prediction){
            return [
                "placeId" => $prediction["place_id"],
                "primaryText" => $prediction["structured_formatting"]["main_text"],
                "secondaryText" => $prediction["structured_formatting"]["secondary_text"],
                "lat" => null,
                "lng" => null
            ];
        });
    }


    private function putInCache(
        Collection $data, string $query, float $lat, float $lng
    ){
        $addresses = collect([]);
        $data->each(function ($item, $key) use($addresses){
            $address = Address::create($item);
            $addresses->push($address->id);
        });
        $store = Query::create(["text"=>$query, "lat"=>$lat, "lng"=>$lng]);

        $store->addresses()->attach($addresses->all());
    }

    private function getFromCache(
        string $query, float $lat, float $lng, float $radius
    ){
        $queries = Query::where("text", "LIKE", "%$query%")->get();
        $queries =  $queries->reject(function($q) use($lat, $lng, $radius){
            return $this->outOfBounds($q, $lat, $lng, $radius);
        });
        $queryAddresses = collect([]);
        foreach ($queries as $q) {
            $addresses = $q->addresses;
            if ($addresses->isNotEmpty()){
                $queryAddresses = $queryAddresses->merge($addresses->all());
            } 
        }
               
        return $queryAddresses->map(function($address){
            return $address->toArray();
        });
    }

    private function outOfBounds($query, float $lat, float $lng, float $radius){
        return $this->distance($query, $lat, $lng) > $radius;
    }

    private function distance($query, $lat, $lng) {
        $p = 0.017453292519943295;    // Math.PI / 180
        $latDiff = ($query->lat) - $lat;
        $b = 0.5 - ( cos( $latDiff * $p) /2 );
        $lngDiff = ($query->lng) - $lng;
        $c = cos($lat * $p) * cos($query->lat * $p) * (1 - cos( $lngDiff * $p)) /2;
        $a = $b + $c;
        return 12742 * asin(sqrt($a)); // 2 * R; R = 6371 km
    }
}