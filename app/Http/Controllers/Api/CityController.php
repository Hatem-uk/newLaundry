<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Laundry;
use App\Models\Agent;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;

class CityController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of cities.
     */
    public function index(Request $request)
    {
        try {
            $query = City::query();

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('name_ar', 'like', "%{$search}%");
                });
            }

            $cities = $query->orderBy('name')->get();

            return $this->successResponse([
                'cities' => $cities,
                'total' => $cities->count()
            ], 200, 'Cities retrieved successfully');

        } catch (\Exception $ex) {
            return $this->errorResponse(null, 500, 'Failed to get cities', $ex->getMessage());
        }
    }

    /**
     * Display the specified city.
     */
    public function show($id)
    {
        try {
            $city = $this->getCityOrFail($id);

            return $this->successResponse($city, 200, 'City retrieved successfully');

        } catch (\Exception $ex) {
            return $this->errorResponse(null, 500, 'Failed to get city', $ex->getMessage());
        }
    }

    /**
     * Get cities by region.
     */
    public function byRegion($region)
    {
        try {
            $cities = City::byRegion($region)->active()->orderBy('name')->get();

            return $this->successResponse([
                'region' => $region,
                'cities' => $cities,
                'total' => $cities->count()
            ], 200, 'Cities by region retrieved successfully');

        } catch (\Exception $ex) {
            return $this->errorResponse(null, 500, 'Failed to get cities by region', $ex->getMessage());
        }
    }

    /**
     * Get all regions.
     */
    public function regions()
    {
        try {
            $regions = City::distinct()->pluck('region')->sort()->values();

            return $this->successResponse([
                'regions' => $regions,
                'total' => $regions->count()
            ], 200, 'Regions retrieved successfully');

        } catch (\Exception $ex) {
            return $this->errorResponse(null, 500, 'Failed to get regions', $ex->getMessage());
        }
    }

    /**
     * Get cities with laundries count.
     */
    public function withLaundries(Request $request)
    {
        try {
            $cities = $this->getCitiesWithCount('laundries', $request);

            return $this->successResponse([
                'cities' => $cities,
                'total' => $cities->count()
            ], 200, 'Cities with laundries retrieved successfully');

        } catch (\Exception $ex) {
            return $this->errorResponse(null, 500, 'Failed to get cities with laundries', $ex->getMessage());
        }
    }

    /**
     * Get cities with agents count.
     */
    public function withAgents(Request $request)
    {
        try {
            $cities = $this->getCitiesWithCount('agents', $request);

            return $this->successResponse([
                'cities' => $cities,
                'total' => $cities->count()
            ], 200, 'Cities with agents retrieved successfully');

        } catch (\Exception $ex) {
            return $this->errorResponse(null, 500, 'Failed to get cities with agents', $ex->getMessage());
        }
    }

    /**
     * Get city or fail
     */
    private function getCityOrFail($id)
    {
        $city = City::find($id);

        if (!$city) {
            throw new \Exception('City not found');
        }
        
        return $city;
    }

    /**
     * Get cities with count for specific relationship
     */
    private function getCitiesWithCount(string $relationship, Request $request)
    {
        $query = City::withCount([$relationship => function($q) {
            $q->online()->active();
        }]);

        // Filter by region
        if ($request->has('region')) {
            $query->byRegion($request->region);
        }

        return $query->orderBy('name')->get();
    }
}
