<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Laundry;
use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServicePurchaseController extends Controller
{
    /**
     * Get available services from nearby laundries
     */
    public function availableServices(Request $request)
    {
        try {
            $customer = $this->getCustomerOrFail();
            $this->validateCustomerCity($customer);

            $radius = $request->get('radius', 50); // Default 50km radius
            
            $services = $this->getServicesFromNearbyLaundries($customer, $radius);

            return response()->json([
                'services' => $services,
                'total_services' => $services->count(),
                'customer_city' => $customer->city->name,
                'search_radius_km' => $radius,
                'available_laundries' => $this->getNearbyLaundriesCount($customer, $radius)
            ]);

        } catch (\Exception $ex) {
            return response()->json(['message' => $ex->getMessage()], 400);
        }
    }

    /**
     * Get services filtered by customer's location and preferences
     */
    public function getServicesByLocation(Request $request)
    {
        try {
            $customer = $this->getCustomerOrFail();
            $this->validateCustomerCity($customer);

            $radius = $request->get('radius', 50);
            $maxDistance = $request->get('max_distance', 100); // Maximum distance filter
            
            $services = $this->getServicesFromNearbyLaundries($customer, $radius, $maxDistance);

            return response()->json([
                'services' => $services,
                'total_services' => $services->count(),
                'customer_city' => $customer->city->name,
                'search_radius_km' => $radius,
                'max_distance_km' => $maxDistance
            ]);

        } catch (\Exception $ex) {
            return response()->json(['message' => $ex->getMessage()], 400);
        }
    }

    /**
     * Purchase agent supply (laundry buys from agent using cash)
     */
    public function purchaseAgentSupply(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = $request->user();
        $service = Service::findOrFail($request->service_id);
        $quantity = $request->quantity;

        // Validate user is a laundry
        if ($user->role !== 'laundry') {
            return response()->json(['message' => 'Only laundries can purchase agent supplies'], 403);
        }

        // Validate service is from an agent
        $agent = $service->agent;
        if (!$agent) {
            return response()->json(['message' => 'Service is not from an agent'], 400);
        }

        // Validate service is available
        if (!$service->isActive()) {
            return response()->json(['message' => 'Service is not available'], 400);
        }

        // Check if service can be purchased with cash
        if (!$service->canBePurchasedWithCash()) {
            return response()->json(['message' => 'Service cannot be purchased with cash'], 400);
        }

        // Calculate total price
        $totalPrice = $service->getCostForQuantity($quantity, false);

        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'recipient_id' => $user->id, // Laundry receives the service
                'provider_id' => $service->provider_id, // Agent provides
                'target_id' => $service->id,
                'target_type' => Service::class,
                'coins' => 0, // No coins involved
                'price' => $totalPrice,
                'status' => 'completed', // Immediate completion for cash purchases
                'meta' => [
                    'quantity' => $quantity,
                    'service_name' => $service->name,
                    'unit_price' => $service->price,
                    'purchase_type' => 'agent_supply'
                ]
            ]);

            // Create invoice
            Invoice::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'payer_id' => $user->id,
                'provider_id' => $service->provider_id,
                'amount' => $totalPrice,
                'payment_method' => 'cash',
                'status' => 'paid', // Immediate payment for cash
                'meta' => [
                    'service_name' => $service->name,
                    'quantity' => $quantity,
                    'agent_name' => $agent->name
                ]
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Agent supply purchased successfully',
                'order' => $order->load(['target', 'provider']),
                'service' => $service,
                'agent' => $agent
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent supply purchase failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Purchase failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Get service details for purchase
     */
    public function getServiceDetails($serviceId)
    {
        $service = Service::where('id', $serviceId)
            ->where('status', 'approved')
            ->with(['provider'])
            ->first();

        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        return response()->json([
            'service' => $service,
            'provider' => $service->provider
        ]);
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
     * Get services from nearby laundries
     */
    private function getServicesFromNearbyLaundries($customer, int $radius, ?int $maxDistance = null)
    {
        // Get nearby laundries with distance information
        $nearbyLaundries = Laundry::getNearbyLaundries($customer->city, $radius);
        
        if ($nearbyLaundries->isEmpty()) {
            throw new \Exception('No online laundries found in your area');
        }

        // Filter by maximum distance if specified
        if ($maxDistance && $maxDistance < $radius) {
            $nearbyLaundries = $nearbyLaundries->filter(function ($laundry) use ($maxDistance) {
                return $laundry->distance_from_customer <= $maxDistance;
            })->values();
        }

        if ($nearbyLaundries->isEmpty()) {
            throw new \Exception('No online laundries found within the specified distance');
        }

        // Get services from nearby laundries
        $services = collect();
        foreach ($nearbyLaundries as $laundry) {
            $laundryServices = $laundry->services()
                ->where('status', 'approved')
                ->with(['provider.city'])
                ->get()
                ->map(function ($service) use ($laundry) {
                    $service->laundry_distance = $laundry->distance_from_customer;
                    return $service;
                });
            
            $services = $services->merge($laundryServices);
        }

        // Sort services by laundry distance (closest first)
        return $services->sortBy('laundry_distance')->values();
    }

    /**
     * Get count of nearby laundries
     */
    private function getNearbyLaundriesCount($customer, int $radius): int
    {
        return Laundry::getNearbyLaundries($customer->city, $radius)->count();
    }
}
