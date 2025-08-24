<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Worker;
use App\Models\Agent;
use App\Models\City;
use App\Models\Service;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LaundryController extends Controller
{
    use ResponseTrait;

    /**
     * Display laundry profile
     */
    public function profile(Request $request)
    {
        $laundry = $this->getLaundryOrFail();
        
        return $this->successResponse([
            'laundry' => $laundry->load(['user', 'city'])
        ], 200, 'Laundry profile retrieved successfully');
    }

    /**
     * Update laundry profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $request->validate([
                'name_ar' => 'sometimes|string|max:255',
                'name_en' => 'sometimes|string|max:255',
                'address_ar' => 'sometimes|string|max:500',
                'address_en' => 'sometimes|string|max:500',
                'phone' => 'sometimes|string|max:20',
                'logo' => 'sometimes|string',
                'city_id' => 'sometimes|exists:cities,id',
                'status' => 'sometimes|in:online,offline,maintenance',
                'working_hours' => 'sometimes|array',
                'delivery_available' => 'sometimes|boolean',
                'pickup_available' => 'sometimes|boolean'
            ]);

            $laundry = $this->getLaundryOrFail();

            $updateData = $this->checkData($request, $laundry);
            $updateData = $this->mergeUpdateData($request, $laundry, $updateData);

            $laundry->update($updateData);

            return $this->successResponse([
                'laundry' => $laundry->fresh()->load(['user', 'city'])
            ], 200, 'Laundry profile updated successfully');

        } catch (\Exception $ex) {
            Log::error('Error updating laundry profile: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to update profile', $ex->getMessage());
        }
    }

    /**
     * Update laundry status
     */
    public function updateStatus(Request $request)
    {
        try {
            $request->validate([
                'status' => 'required|in:online,offline,maintenance'
            ]);

            $laundry = $this->getLaundryOrFail();
            $laundry->update(['status' => $request->status]);

            return $this->successResponse([
                'status' => $laundry->status,
                'status_label' => $laundry->status_label
            ], 200, 'Laundry status updated successfully');

        } catch (\Exception $ex) {
            Log::error('Error updating laundry status: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to update status', $ex->getMessage());
        }
    }

    /**
     * Get laundry statistics with enhanced information
     */
    public function statistics(Request $request)
    {
        try {
            $laundry = $this->getLaundryOrFail();
            
            $stats = $this->calculateLaundryStats($laundry);

            return $this->successResponse($stats, 200, 'Laundry statistics retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting laundry statistics: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get statistics', $ex->getMessage());
        }
    }

    /**
     * Get nearby agents for the laundry, ordered by proximity to customer
     */
    public function getNearbyAgents(Request $request)
    {
        try {
            $laundry = $request->user()->laundry;
            
            if (!$laundry) {
                return $this->notFoundResponse('Laundry profile not found');
            }

            if (!$laundry->city) {
                return $this->errorResponse(null, 422, 'Laundry city not set');
            }

            $radius = $request->get('radius', 50); // Default 50km radius
            $maxDistance = $request->get('max_distance', $radius);
            $status = $request->get('status', 'online');
            
            // Get agents that can serve the laundry's city and nearby cities
            $agents = Agent::getAgentsForCity($laundry->city, $radius);

            // Apply additional filters
            if ($maxDistance < $radius) {
                $agents = $agents->filter(function ($agent) use ($maxDistance) {
                    return $agent->distance_from_laundry <= $maxDistance;
                })->values();
            }

            if ($status) {
                $agents = $agents->filter(function ($agent) use ($status) {
                    return $agent->status === $status;
                })->values();
            }

            return $this->successResponse([
                'agents' => $agents,
                'total' => $agents->count(),
                'laundry_city' => $laundry->city->name,
                'search_radius_km' => $radius,
                'max_distance_km' => $maxDistance,
                'filters_applied' => [
                    'status' => $status,
                    'max_distance' => $maxDistance
                ]
            ], 200, 'Nearby agents retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting nearby agents: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get nearby agents', $ex->getMessage());
        }
    }

    /**
     * Get agents by specific city for the laundry
     */
    public function getAgentsByCity(Request $request, $cityId)
    {
        try {
            $laundry = $request->user()->laundry;
            
            if (!$laundry) {
                return $this->notFoundResponse('Laundry profile not found');
            }

            $city = City::find($cityId);
            if (!$city) {
                return $this->notFoundResponse('City not found');
            }

            $radius = $request->get('radius', 50);
            $status = $request->get('status', 'online');
            
            // Get agents that can serve the specified city
            $agents = Agent::getAgentsForCity($city, $radius);

            // Apply status filter
            if ($status) {
                $agents = $agents->filter(function ($agent) use ($status) {
                    return $agent->status === $status;
                })->values();
            }

            return $this->successResponse([
                'agents' => $agents,
                'total' => $agents->count(),
                'target_city' => $city->name,
                'laundry_city' => $laundry->city->name,
                'search_radius_km' => $radius,
                'filters_applied' => [
                    'status' => $status
                ]
            ], 200, 'Agents by city retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting agents by city: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get agents by city', $ex->getMessage());
        }
    }

    /**
     * Get laundry's services with enhanced filtering
     */
    public function getServices(Request $request)
    {
        try {
            $laundry = $request->user()->laundry;
            
            if (!$laundry) {
                return $this->notFoundResponse('Laundry profile not found');
            }

            $query = $laundry->services();
            
            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }
            
            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }
            
            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            $services = $query->orderBy('created_at', 'desc')->get();

            return $this->successResponse([
                'services' => $services,
                'total' => $services->count(),
                'filters_applied' => $request->only(['status', 'type', 'min_price', 'max_price'])
            ], 200, 'Laundry services retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting laundry services: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get services', $ex->getMessage());
        }
    }

    public function indexWorkers(Request $request)
    {
        try {
            $workers = Worker::where('laundry_id', $request->user()->laundry->id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse([
                'workers' => $workers
            ], 200, 'Workers retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting workers: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get workers', $ex->getMessage());
        }
    }

    public function addWorker(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'position' => 'required',
                'salary' => 'required|numeric',
                'status' => 'sometimes|in:pending,approved,rejected',
                'phone' => 'sometimes|nullable|string'
            ]);

            $laundryUser = $request->user();
            $laundry = $laundryUser->laundry;
            
            if (!$laundry) {
                return $this->errorResponse(null, 422, 'Laundry profile not found');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'role' => 'worker',
                'status' => $request->status ?? 'pending', // Requires laundry approval
                'phone' => $request->phone ?? null
            ]);

            $worker = $user->worker()->create([
                'laundry_id' => $laundry->id,
                'position' => $request->position,
                'salary' => $request->salary,
                'phone' => $request->phone ?? null
            ]);

            return $this->successResponse([
                'worker_id' => $worker->id
            ], 201, 'Worker created successfully');

        } catch (\Exception $ex) {
            Log::error('Error creating worker: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to create worker', $ex->getMessage());
        }
    }

    public function pendingWorkers(Request $request)
    {
        try {
            $workers = Worker::where('laundry_id', $request->user()->laundry->id)
                ->whereHas('user', function($query) {
                    $query->where('status', 'pending');
                })
                ->with('user')
                ->get();

            return $this->successResponse($workers, 200, 'Pending workers retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting pending workers: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get pending workers', $ex->getMessage());
        }
    }

    public function approveWorker(Request $request, $worker)
    {
        try {
            $worker = Worker::where('id', $worker)
                ->where('laundry_id', $request->user()->laundry->id)
                ->first();

            if (!$worker) {
                return $this->notFoundResponse('Worker not found');
            }

            if ($worker->user->status === 'approved') {
                return $this->errorResponse(null, 400, 'Worker is already approved');
            }

            $worker->user->update(['status' => 'approved']);
            
            return $this->successResponse([
                'worker' => $worker->load('user')
            ], 200, 'Worker approved successfully');

        } catch (\Exception $ex) {
            Log::error('Error approving worker: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to approve worker', $ex->getMessage());
        }
    }

    /**
     * Validate and prepare laundry data with translations
     */
    private function checkData(Request $request, ?\App\Models\Laundry $laundry = null): array
    {
        $rules = [
            'name_ar' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'address_ar' => 'nullable|string|max:500',
            'address_en' => 'nullable|string|max:500',
        ];

        $validated = $request->validate($rules);

        return [
            'name' => Helper::translateData($request, 'name_ar', 'name_en') 
                     ?? optional($laundry)->name,
            'address' => Helper::translateData($request, 'address_ar', 'address_en') 
                       ?? optional($laundry)->address,
        ];
    }

    /**
     * Merge update data with existing values
     */
    private function mergeUpdateData(Request $request, $laundry, array $updateData): array
    {
        return array_merge($updateData, [
            'city_id' => $request->city_id ?? $laundry->city_id,
            'status' => $request->status ?? $laundry->status,
            'phone' => $request->phone ?? $laundry->phone,
            'logo' => $request->logo ?? $laundry->logo,
            'working_hours' => $request->working_hours ?? $laundry->working_hours,
            'delivery_available' => $request->delivery_available ?? $laundry->delivery_available,
            'pickup_available' => $request->pickup_available ?? $laundry->pickup_available
        ]);
    }

    /**
     * Calculate laundry statistics
     */
    private function calculateLaundryStats($laundry): array
    {
        return [
            'total_orders' => Order::where('provider_id', $laundry->user_id)->count(),
            'pending_orders' => Order::where('provider_id', $laundry->user_id)->byStatus('pending')->count(),
            'in_process_orders' => Order::where('provider_id', $laundry->user_id)->byStatus('in_process')->count(),
            'completed_orders' => Order::where('provider_id', $laundry->user_id)->byStatus('completed')->count(),
            'total_revenue' => Order::where('provider_id', $laundry->user_id)->sum('price'),
            'total_coins_earned' => Order::where('provider_id', $laundry->user_id)->where('coins', '>', 0)->sum('coins'),
            'average_rating' => $laundry->average_rating,
            'total_ratings' => $laundry->total_ratings,
            'services_count' => Service::where('provider_id', $laundry->user_id)->count(),
            'active_services' => Service::where('provider_id', $laundry->user_id)->where('status', 'approved')->count(),
            'workers_count' => Worker::where('laundry_id', $laundry->id)->count(),
            'active_workers' => Worker::where('laundry_id', $laundry->id)->where('status', 'active')->count()
        ];
    }

    /**
     * Get laundry profile or fail
     */
    private function getLaundryOrFail()
    {
        $laundry = request()->user()->laundry;
        
        if (!$laundry) {
            throw new \Exception('Laundry profile not found');
        }
        
        return $laundry;
    }
}