<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use App\Helpers\Helper;
use App\Mail\MailOrders;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the laundry's services.
     */
    public function index(Request $request)
    {
        try {
            $laundry = $this->getLaundryOrFail();
            
            $services = Service::where('provider_id', $laundry->user_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse([
                'services' => $services
            ], 200, 'Services retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting services: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get services', $ex->getMessage());
        }
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        try {
            $this->validateServiceData($request);
            $this->validateServicePricing($request);

            $laundry = $this->getLaundryOrFail();

            $imagePath = $this->handleImageUpload($request);
            $serviceData = $this->prepareServiceData($request, $laundry->user_id, $imagePath);

            $service = Service::create($serviceData);

            // Send email notification to admin
            $this->sendNewServiceNotification($service);

            return $this->successResponse([
                'service' => $service
            ], 201, 'Service created successfully');

        } catch (\Exception $ex) {
            Log::error('Error creating service: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to create service', $ex->getMessage());
        }
    }

    /**
     * Display the specified service.
     */
    public function show(Request $request, $id)
    {
        try {
            $laundry = $this->getLaundryOrFail();
            
            $service = $this->getServiceOrFail($id, $laundry->user_id);

            return $this->successResponse($service, 200, 'Service retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting service: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get service', $ex->getMessage());
        }
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validateServiceData($request, false);
            
            $laundry = $this->getLaundryOrFail();
            $service = $this->getServiceOrFail($id, $laundry->user_id);

            // Only allow updates if service is pending
            if ($service->status !== 'pending') {
                return $this->errorResponse(null, 422, 'Cannot update service that is not pending');
            }

            $updateData = $this->prepareUpdateData($request, $service);
            $this->validateServicePricing($updateData);

            $this->handleImageUpdate($request, $service, $updateData);

            $service->update($updateData);

            return $this->successResponse([
                'service' => $service->fresh()
            ], 200, 'Service updated successfully');

        } catch (\Exception $ex) {
            Log::error('Error updating service: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to update service', $ex->getMessage());
        }
    }

    /**
     * Remove the specified service.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $laundry = $this->getLaundryOrFail();
            $service = $this->getServiceOrFail($id, $laundry->user_id);

            // Only allow deletion if service is pending
            if ($service->status !== 'pending') {
                return $this->errorResponse(null, 422, 'Cannot delete service that is not pending');
            }

            // Delete image if exists
            Helper::deleteFile($service->image);
            $service->delete();

            return $this->successResponse(null, 200, 'Service deleted successfully');

        } catch (\Exception $ex) {
            Log::error('Error deleting service: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to delete service', $ex->getMessage());
        }
    }

    /**
     * Get service statistics
     */
    public function statistics(Request $request)
    {
        try {
            $laundry = $this->getLaundryOrFail();
            
            $stats = $this->calculateServiceStats($laundry->user_id);

            return $this->successResponse($stats, 200, 'Service statistics retrieved successfully');

        } catch (\Exception $ex) {
            Log::error('Error getting service statistics: ', ['error' => $ex->getMessage()]);
            return $this->errorResponse(null, 500, 'Failed to get statistics', $ex->getMessage());
        }
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

    /**
     * Get service or fail
     */
    private function getServiceOrFail($id, $providerId)
    {
        $service = Service::where('id', $id)
            ->where('provider_id', $providerId)
            ->first();

        if (!$service) {
            throw new \Exception('Service not found');
        }
        
        return $service;
    }

    /**
     * Validate service data
     */
    private function validateServiceData(Request $request, bool $isCreate = true): void
    {
        $rules = [
            'name_ar' => $isCreate ? 'required|string|max:255' : 'nullable|string|max:255',
            'name_en' => $isCreate ? 'required|string|max:255' : 'nullable|string|max:255',
            'description_ar' => $isCreate ? 'required|string' : 'nullable|string',
            'description_en' => $isCreate ? 'required|string' : 'nullable|string',
            'quantity' => $isCreate ? 'required|integer|min:1' : 'sometimes|integer|min:1',
            'type' => $isCreate ? 'required|string|in:washing,ironing,agent_supply,cleaning,other' : 'sometimes|string|in:washing,ironing,agent_supply,cleaning,other',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        $request->validate($rules);
    }

    /**
     * Validate service pricing
     */
    private function validateServicePricing($data): void
    {
        $coinCost = is_array($data) ? ($data['coin_cost'] ?? null) : $data->coin_cost;
        $price = is_array($data) ? ($data['price'] ?? null) : $data->price;
        
        if (!$coinCost && !$price) {
            throw new \Exception('Either coin_cost or price must be provided');
        }
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload(Request $request): ?string
    {
        if ($request->hasFile('image')) {
            return Helper::uploadFile($request->file('image'), 'services');
        }
        return null;
    }

    /**
     * Handle image update
     */
    private function handleImageUpdate(Request $request, Service $service, array &$updateData): void
    {
        if ($request->hasFile('image')) {
            // Delete old image if exists
            Helper::deleteFile($service->image);
            $imagePath = Helper::uploadFile($request->file('image'), 'services');
            if ($imagePath) {
                $updateData['image'] = $imagePath;
            }
        }
    }

    /**
     * Prepare service data for creation
     */
    private function prepareServiceData(Request $request, int $providerId, ?string $imagePath): array
    {
        $serviceData = $this->checkData($request);
        $serviceData['provider_id'] = $providerId;
        $serviceData['image'] = $imagePath;
        $serviceData['status'] = 'pending';
        $serviceData['quantity'] = $request->quantity;
        $serviceData['type'] = $request->type;
        $serviceData['coin_cost'] = $request->coin_cost;
        $serviceData['price'] = $request->price;

        return $serviceData;
    }

    /**
     * Prepare update data
     */
    private function prepareUpdateData(Request $request, Service $service): array
    {
        $updateData = $this->checkData($request, $service);
        $updateData['quantity'] = $request->quantity ?? $service->quantity;
        $updateData['type'] = $request->type ?? $service->type;
        $updateData['coin_cost'] = $request->coin_cost ?? $service->coin_cost;
        $updateData['price'] = $request->price ?? $service->price;

        return $updateData;
    }

    /**
     * Calculate service statistics
     */
    private function calculateServiceStats(int $providerId): array
    {
        return [
            'total_services' => Service::where('provider_id', $providerId)->count(),
            'active_services' => Service::where('provider_id', $providerId)->where('status', 'approved')->count(),
            'pending_services' => Service::where('provider_id', $providerId)->where('status', 'pending')->count(),
            'services_by_type' => Service::where('provider_id', $providerId)
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
            'coin_services' => Service::where('provider_id', $providerId)->whereNotNull('coin_cost')->count(),
            'cash_services' => Service::where('provider_id', $providerId)->whereNotNull('price')->count()
        ];
    }

    /**
     * Send new service notification
     */
    private function sendNewServiceNotification(Service $service): void
    {
        try {
            MailOrders::sendNewServiceNotification($service);
        } catch (\Exception $e) {
            Log::error('Failed to send new service notification email: ' . $e->getMessage());
        }
    }

    /**
     * Validate and prepare service data with translations
     */
    private function checkData(Request $request, ?Service $service = null): array
    {
        $rules = [
            'name_ar' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
        ];

        $validated = $request->validate($rules);

        return [
            'name' => Helper::translateData($request, 'name_ar', 'name_en') 
                     ?? optional($service)->name,
            'description' => Helper::translateData($request, 'description_ar', 'description_en') 
                           ?? optional($service)->description,
        ];
    }
}


