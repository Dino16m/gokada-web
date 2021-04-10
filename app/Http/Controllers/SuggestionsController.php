<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeocodingService;
use App\Services\SuggestionService;
use App\Services\ReverseGeocodeService;

class SuggestionsController extends Controller
{
    private $suggestionService;
    private $geocodeService;
    private $reverseGeocodeService;

    public function __construct()
    {
        $apiKey = config('services.googlemaps.key');
        $suggestionUrl = config('services.googlemaps.autocomplete_url');
        $this->suggestionService = new SuggestionService($apiKey, $suggestionUrl);

        $geocodeUrl = config('services.googlemaps.geocode_url');
        $this->geocodeService = new GeocodingService($apiKey, $geocodeUrl);

        $reverseGeocodeUrl = \config('services.googlemaps.reverse_geocode_url');
        $this->reverseGeocodeService = new ReverseGeocodeService(
            $apiKey, $reverseGeocodeUrl
        );
    }

    public function query(Request $request)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $query = $request->input('q') ?? "";
        $radius = $request->query('r');
        $results = $this->suggestionService->getSuggestions(
            $query, $lat, $lng, $radius
        );

        return $this->jsonResponse(['status' => true, 'data' => $results]);
    }

    public function geocode(Request $request)
    {
        $placeId = $request->query('place_id');
        try {
            $results = $this->geocodeService->geocode($placeId);
        } catch (\Exception $e) {
            return $this->jsonResponse(
                ['status' => false, 'error' => $e->getMessage()]
            );
        }

        return $this->jsonResponse(['status' => true, 'data' => $results]);
    }
    
    public function reverse(Request $request)
    {
        $lat = $request->query("lat");
        $lng = $request->query("lng");
        try {
            $result = $this->reverseGeocodeService->reverse($lat, $lng);
        } catch (\Exception $e) {
            return $this->jsonResponse(
                ['status' => false, 'error' => $e->getMessage()]
            );
        }
        return $this->jsonResponse(['status' => true, 'data' => $result]);
    }
}
