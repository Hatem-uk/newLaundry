<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Laundry;
use App\Models\Agent;
use App\Models\City;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * Display a listing of services
     */
    public function index()
    {
        $services = Service::with(['laundry.user', 'agent.user'])
            ->latest()
            ->paginate(20);

        // Debug: Log the services to see what's being loaded
        \Log::info('Services loaded:', ['count' => $services->count()]);

        return view('admin.services', compact('services'));
    }

    /**
     * Show the form for creating a new service
     */
    public function create()
    {
        $laundries = Laundry::with('user')->where('is_active', true)->get();
        $agents = Agent::with('user')->where('is_active', true)->get();
        $cities = City::all();
        
        return view('admin.services.create', compact('laundries', 'agents', 'cities'));
    }

    /**
     * Store a newly created service
     */
    public function store(Request $request)
    {
        $validated = $this->validateServiceCreation($request);
        
        try {
            $service = $this->createService($validated);
            
            return redirect()
                ->route('admin.services')
                ->with('success', 'تم إنشاء الخدمة بنجاح');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', Helper::messageErrorException($e));
        }
    }

    /**
     * Display the specified service
     */
    public function show(Service $service)
    {
        $service->load(['provider', 'laundry', 'agent', 'orders.user']);
        
        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service
     */
    public function edit(Service $service)
    {
        $laundries = Laundry::with('user')->where('is_active', true)->get();
        $agents = Agent::with('user')->where('is_active', true)->get();
        $cities = City::all();
        
        return view('admin.services.edit', compact('service', 'laundries', 'agents', 'cities'));
    }

    /**
     * Update the specified service
     */
    public function update(Request $request, Service $service)
    {
        $validated = $this->validateServiceUpdate($request, $service);
        
        try {
            $this->updateService($service, $validated);
            
            return redirect()
                ->route('admin.services.show', $service)
                ->with('success', 'تم تحديث الخدمة بنجاح');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', Helper::messageErrorException($e));
        }
    }

    /**
     * Remove the specified service
     */
    public function destroy(Service $service)
    {
        try {
            // Delete service image if exists
            if ($service->image) {
                Helper::deleteFile($service->image);
            }
            
            $service->delete();
            
            return redirect()
                ->route('admin.services')
                ->with('success', 'تم حذف الخدمة بنجاح');
                
        } catch (\Exception $e) {
            return back()->with('error', Helper::messageErrorException($e));
        }
    }

    /**
     * Change service status
     */
    public function changeStatus(Request $request, Service $service)
    {
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'active', 'inactive', 'approved', 'rejected'])]
        ]);

        try {
            $service->update(['status' => $request->status]);
            
            return back()->with('success', 'تم تغيير حالة الخدمة بنجاح');
            
        } catch (\Exception $e) {
            return back()->with('error', Helper::messageErrorException($e));
        }
    }

    /**
     * Validate service creation
     */
    private function validateServiceCreation(Request $request): array
    {
        return $request->validate([
            'provider_type' => 'required|in:laundry,agent',
            'provider_id' => 'required|exists:users,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'coin_cost' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'type' => 'required|string|max:100',
            'status' => 'nullable|in:pending,active,inactive,approved,rejected',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    }

    /**
     * Validate service update
     */
    private function validateServiceUpdate(Request $request, Service $service): array
    {
        return $request->validate([
            'provider_type' => 'nullable|in:laundry,agent',
            'provider_id' => 'nullable|exists:users,id',
            'name_ar' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'coin_cost' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'type' => 'nullable|string|max:100',
            'status' => 'nullable|in:pending,active,inactive,approved,rejected',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    }

    /**
     * Create service
     */
    private function createService(array $validated): Service
    {
        $imagePath = null;
        if (request()->hasFile('image')) {
            $imagePath = Helper::uploadFile(
                request()->file('image'),
                'service_images',
                ['jpeg', 'png', 'jpg', 'gif'],
                2048
            );
        }

        return Service::create([
            'provider_id' => $validated['provider_id'],
            'name' => [
                'ar' => $validated['name_ar'],
                'en' => $validated['name_en']
            ],
            'description' => [
                'ar' => $validated['description_ar'] ?? null,
                'en' => $validated['description_en'] ?? null
            ],
            'coin_cost' => $validated['coin_cost'] ?? null,
            'price' => $validated['price'] ?? null,
            'quantity' => $validated['quantity'] ?? 1,
            'type' => $validated['type'],
            'status' => $validated['status'] ?? 'pending',
            'image' => $imagePath,
        ]);
    }

    /**
     * Update service
     */
    private function updateService(Service $service, array $validated): void
    {
        $updateData = [];
        
        // Update name if provided
        if (isset($validated['name_ar']) || isset($validated['name_en'])) {
            $currentName = $service->getRawOriginal('name') ?: [];
            $updateData['name'] = [
                'ar' => $validated['name_ar'] ?? $currentName['ar'] ?? '',
                'en' => $validated['name_en'] ?? $currentName['en'] ?? ''
            ];
        }
        
        // Update description if provided
        if (isset($validated['description_ar']) || isset($validated['description_en'])) {
            $currentDescription = $service->getRawOriginal('description') ?: [];
            $updateData['description'] = [
                'ar' => $validated['description_ar'] ?? $currentDescription['ar'] ?? null,
                'en' => $validated['description_en'] ?? $currentDescription['en'] ?? null
            ];
        }
        
        // Update other fields if provided
        if (isset($validated['provider_id'])) $updateData['provider_id'] = $validated['provider_id'];
        if (isset($validated['coin_cost'])) $updateData['coin_cost'] = $validated['coin_cost'];
        if (isset($validated['price'])) $updateData['price'] = $validated['price'];
        if (isset($validated['quantity'])) $updateData['quantity'] = $validated['quantity'];
        if (isset($validated['type'])) $updateData['type'] = $validated['type'];
        if (isset($validated['status'])) $updateData['status'] = $validated['status'];
        
        // Handle image upload
        if (request()->hasFile('image')) {
            // Delete old image
            if ($service->image) {
                Helper::deleteFile($service->image);
            }
            
            $updateData['image'] = Helper::uploadFile(
                request()->file('image'),
                'service_images',
                ['jpeg', 'png', 'jpg', 'gif'],
                2048
            );
        }
        
        $service->update($updateData);
    }
}
