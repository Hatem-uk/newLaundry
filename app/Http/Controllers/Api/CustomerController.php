<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Laundry;
use App\Models\Agent;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    use ResponseTrait;

    /**
     * Display customer profile with full information
     */
    public function profile(Request $request)
    {
        $customer = $request->user()->customer;
        
        if (!$customer) {
            return $this->notFoundResponse('Customer profile not found');
        }

        // Get customer statistics
        $user = $request->user();
        $stats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'pending_orders' => Order::where('user_id', $user->id)->byStatus('pending')->count(),
            'completed_orders' => Order::where('user_id', $user->id)->byStatus('completed')->count(),
            'total_spent_coins' => abs(Order::where('user_id', $user->id)->where('coins', '<', 0)->sum('coins')),
            'total_received_coins' => Order::where('user_id', $user->id)->where('coins', '>', 0)->sum('coins'),
            'total_ratings_given' => $customer->ratings()->count(),
            'average_rating_given' => $customer->ratings()->avg('rating') ?? 0
        ];

        return $this->successResponse([
            'customer' => $customer->load(['user', 'city']),
            'statistics' => $stats
        ], 200, 'Customer profile retrieved successfully');
    }

    /**
     * Update customer profile with validation
     */
    public function updateProfile(Request $request)
    {
        try {
            $request->validate([
                'address_ar' => 'nullable|string|max:255',
                'address_en' => 'nullable|string|max:255',
                'phone' => 'sometimes|string|max:20',
                'city_id' => 'sometimes|exists:cities,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $customer = $request->user()->customer;
            
            if (!$customer) {
                return $this->notFoundResponse('Customer profile not found');
            }

            $updateData = $this->checkData($request, $customer);
            $updateData['city_id'] = $request->city_id ?? $customer->city_id;
            $updateData['phone'] = $request->phone ?? $customer->phone;

            if ($request->hasFile('image')) {
                // Handle image upload
                $imagePath = Helper::uploadFile($request->file('image'), 'customers');
                if ($imagePath) {
                    // Delete old image if exists
                    Helper::deleteFile($customer->image);
                    $updateData['image'] = $imagePath;
                }
            }

            $customer->update($updateData);

            return $this->successResponse([
                'customer' => $customer->fresh()->load(['user', 'city'])
            ], 200, 'Profile updated successfully');

        } catch (\Exception $ex) {
            Log::error('Error updating customer profile: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to update profile', $ex->getMessage());
        }
    }

    /**
     * Get nearby laundries for the customer with advanced filtering
     */
    public function getNearbyLaundries(Request $request)
    {
        try {
            $customer = $this->getCustomerOrFail();
            $this->validateCustomerCity($customer);

            $filters = $this->prepareNearbyFilters($request);
            $laundries = Laundry::getNearbyLaundries($customer->city, $filters['radius']);
            
            $filteredLaundries = $this->applyNearbyFilters($laundries, $filters);

            return $this->successResponse([
                'laundries' => $filteredLaundries,
                'total' => $filteredLaundries->count(),
                'customer_city' => $customer->city->name,
                'search_radius_km' => $filters['radius'],
                'max_distance_km' => $filters['max_distance'],
                'filters_applied' => [
                    'status' => $filters['status'],
                    'max_distance' => $filters['max_distance']
                ]
            ], 200, 'Nearby laundries retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting nearby laundries: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get nearby laundries', $ex->getMessage());
        }
    }

    /**
     * Get nearby agents for the customer with advanced filtering
     */
    public function getNearbyAgents(Request $request)
    {
        try {
            $customer = $this->getCustomerOrFail();
            $this->validateCustomerCity($customer);

            $filters = $this->prepareNearbyFilters($request);
            $agents = Agent::getAgentsForCity($customer->city, $filters['radius']);
            
            $filteredAgents = $this->applyNearbyFilters($agents, $filters);

            return $this->successResponse([
                'agents' => $filteredAgents,
                'total' => $filteredAgents->count(),
                'customer_city' => $customer->city->name,
                'search_radius_km' => $filters['radius'],
                'max_distance_km' => $filters['max_distance'],
                'filters_applied' => [
                    'status' => $filters['status'],
                    'max_distance' => $filters['max_distance']
                ]
            ], 200, 'Nearby agents retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting nearby agents: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get nearby agents', $ex->getMessage());
        }
    }

    /**
     * Get customer's favorite services
     */
    public function getFavoriteServices(Request $request)
    {
        try {
            $customer = $request->user()->customer;
            
            if (!$customer) {
                return $this->notFoundResponse('Customer profile not found');
            }

            // Get services from orders where customer has ordered multiple times
            $favoriteServices = Order::where('user_id', $request->user()->id)
                ->where('target_type', 'service')
                ->select('target_id', DB::raw('COUNT(*) as order_count'))
                ->groupBy('target_id')
                ->having('order_count', '>', 1)
                ->orderBy('order_count', 'desc')
                ->limit(10)
                ->get();

            $services = Service::whereIn('id', $favoriteServices->pluck('target_id'))
                ->with(['provider.city'])
                ->get()
                ->map(function ($service) use ($favoriteServices) {
                    $favorite = $favoriteServices->where('target_id', $service->id)->first();
                    $service->order_count = $favorite->order_count;
                    return $service;
                })
                ->sortByDesc('order_count');

            return $this->successResponse([
                'favorite_services' => $services,
                'total' => $services->count()
            ], 200, 'Favorite services retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting favorite services: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get favorite services', $ex->getMessage());
        }
    }

    /**
     * Get customer's recent searches (based on order history)
     */
    public function getRecentSearches(Request $request)
    {
        try {
            $customer = $request->user()->customer;
            
            if (!$customer) {
                return $this->notFoundResponse('Customer profile not found');
            }

            $recentSearches = Order::where('user_id', $request->user()->id)
                ->where('target_type', 'service')
                ->with(['target.provider.city'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($order) {
                    return [
                        'service_name' => $order->target->name,
                        'provider_name' => $order->target->provider->name,
                        'city' => $order->target->provider->city->name,
                        'ordered_at' => $order->created_at,
                        'service_id' => $order->target_id
                    ];
                });

            return $this->successResponse([
                'recent_searches' => $recentSearches,
                'total' => $recentSearches->count()
            ], 200, 'Recent searches retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting recent searches: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get recent searches', $ex->getMessage());
        }
    }

    /**
     * Validate and prepare customer data with translations
     */
    private function checkData(Request $request, ?Customer $customer = null): array
    {
        $rules = [
            'address_ar' => 'nullable|string|max:255',
            'address_en' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ];

        $validated = $request->validate($rules);

        return [
            'address' => Helper::translateData($request, 'address_ar', 'address_en') 
                       ?? optional($customer)->address,
        ];
    }

    /**
     * Get customer profile or fail
     */
    private function getCustomerOrFail()
    {
        $customer = request()->user()->customer;
        
        if (!$customer) {
            throw new \Exception('Customer profile not found');
        }
        
        return $customer;
    }

    /**
     * Validate customer has city set
     */
    private function validateCustomerCity($customer): void
    {
        if (!$customer->city) {
            throw new \Exception('Customer city not set');
        }
    }

    /**
     * Prepare nearby search filters
     */
    private function prepareNearbyFilters(Request $request): array
    {
        $radius = $request->get('radius', 50); // Default 50km radius
        
        return [
            'radius' => $radius,
            'max_distance' => $request->get('max_distance', $radius),
            'status' => $request->get('status', 'online')
        ];
    }

    /**
     * Apply nearby search filters to collection
     */
    private function applyNearbyFilters($collection, array $filters)
    {
        // Apply distance filter
        if ($filters['max_distance'] < $filters['radius']) {
            $collection = $collection->filter(function ($item) use ($filters) {
                return $item->distance_from_customer <= $filters['max_distance'];
            })->values();
        }

        // Apply status filter
        if ($filters['status']) {
            $collection = $collection->filter(function ($item) use ($filters) {
                return $item->status === $filters['status'];
            })->values();
        }

        return $collection;
    }
}
